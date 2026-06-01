<?php
/*
 * Authentication helper functions.
 * Handles user registration, login, session management, and current user lookup.
 */


require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Logger.php';

/* Register a new user. Store creation is optional — pass storeName to create a store. */

function auth_register($name, $email, $password, $storeName = '', $storeDescription = '', $contactPhone = '', $phone = '')
{
    try {
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Missing required fields'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }

        $db = db_get_connection();

        $userExists = $db->prepare('SELECT id FROM users WHERE email = ?');
        $userExists->execute([$email]);
        if ($userExists->fetch()) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $userId = auth_generate_id();
        $userPhone = $phone ?: $contactPhone;

        $db->beginTransaction();
        try {
            $userStmt = $db->prepare('INSERT INTO users (id, email, password, name, phone, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            $userStmt->execute([$userId, $email, $hashedPassword, $name, $userPhone]);

            // Seed the user with 50 free tokens
            $db->exec("UPDATE users SET token_balance = COALESCE(token_balance, 0) + 50 WHERE id = '{$userId}'");

            $storeSlug = null;
            if (!empty($storeName)) {
                $slug = auth_create_slug($storeName);

                $slugExists = $db->prepare('SELECT id FROM stores WHERE slug = ?');
                $slugExists->execute([$slug]);
                if ($slugExists->fetch()) {
                    $db->rollBack();
                    return ['success' => false, 'error' => 'Store name already taken'];
                }

                $contact = $phone ?: $contactPhone;
                $storeId = auth_generate_id();
                $storeStmt = $db->prepare('INSERT INTO stores (id, name, slug, description, owner_id, contact_phone, contact_email, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())');
                $storeStmt->execute([$storeId, $storeName, $slug, $storeDescription, $userId, $contact, $email]);
                $storeSlug = $slug;
            }

            $db->commit();

            return [
                'success' => true,
                'userId' => $userId,
                'storeSlug' => $storeSlug,
                'message' => 'Registration successful'
            ];
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/* Authenticate a user and create a session token. */

function auth_login($email, $password)
{
    try {
        $db = db_get_connection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }

        $sessionId = auth_generate_id();
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        $sessionStmt = $db->prepare('INSERT INTO sessions (id, user_id, token, expires_at, created_at) VALUES (?, ?, ?, ?, NOW())');
        $sessionStmt->execute([$sessionId, $user['id'], $tokenHash, $expiresAt]);

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        setcookie('vomp_token', $token, [
            'expires' => strtotime($expiresAt),
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        logger_info('User logged in: ' . $user['id']);

        return [
            'success' => true,
            'userId' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'token' => $token
        ];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/* Return the currently authenticated user from the stored token. */

function auth_get_current_user()
{
    if (!isset($_COOKIE['vomp_token'])) {
        return null;
    }

    $db = db_get_connection();
    $tokenHash = hash('sha256', $_COOKIE['vomp_token']);
    $stmt = $db->prepare('SELECT users.* FROM users JOIN sessions ON users.id = sessions.user_id WHERE sessions.token = ? AND sessions.expires_at > NOW()');
    $stmt->execute([$tokenHash]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* Clear the current user session and remove the session cookie. */

function auth_logout()
{
    if (isset($_COOKIE['vomp_token'])) {
        $db = db_get_connection();
        $tokenHash = hash('sha256', $_COOKIE['vomp_token']);
        $stmt = $db->prepare('DELETE FROM sessions WHERE token = ?');
        $stmt->execute([$tokenHash]);
        logger_info('User logged out (token removed)');
    }

    setcookie('vomp_token', '', ['expires' => time() - 3600, 'path' => '/', 'samesite' => 'Lax']);
}

/* Update user name, email, and optionally password. */

function auth_update_user($userId, $data)
{
    $allowed = ['name', 'email', 'phone'];
    if (!empty($data['password'])) {
        $allowed[] = 'password';
    }
    $updateData = array_intersect_key($data, array_flip($allowed));

    if (empty($updateData)) {
        return ['success' => false, 'error' => 'No data to update'];
    }

    if (isset($updateData['email'])) {
        if (!filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }
        $db = db_get_connection();
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $stmt->execute([$updateData['email'], $userId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Email already in use'];
        }
    }

    if (isset($updateData['password'])) {
        $updateData['password'] = password_hash($updateData['password'], PASSWORD_BCRYPT);
    }

    $updateData['updated_at'] = date('Y-m-d H:i:s');
    $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($updateData)));
    $sql = "UPDATE users SET $set WHERE id = ?";

    $db = db_get_connection();
    $stmt = $db->prepare($sql);
    $result = $stmt->execute(array_merge(array_values($updateData), [$userId]));

    return $result ? ['success' => true, 'message' => 'Profile updated'] : ['success' => false, 'error' => 'Failed to update profile'];
}

/* Check if the current user is an admin. */
function auth_is_admin()
{
    $user = auth_get_current_user();
    return $user && ($user['role'] ?? 'user') === 'admin';
}

/* Normalize text into a URL-friendly store slug. */

function auth_create_slug($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/* Generate a random unique identifier for users, stores, or sessions. */

function auth_generate_id()
{
    return bin2hex(random_bytes(12));
}
