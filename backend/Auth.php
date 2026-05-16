<?php

namespace Backend;
require_once __DIR__ . '/Logger.php';

class Auth
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register($name, $email, $password, $storeName, $storeDescription, $contactPhone)
    {
        try {
            // Validate input
            if (empty($name) || empty($email) || empty($password) || empty($storeName) || empty($contactPhone)) {
                return ['success' => false, 'error' => 'Missing required fields'];
            }

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'error' => 'Invalid email format'];
            }

            // Check if user exists
            $userExists = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $userExists->execute([$email]);
            if ($userExists->fetch()) {
                return ['success' => false, 'error' => 'Email already registered'];
            }

            // Create store slug
            $storeSlug = $this->createSlug($storeName);

            // Check if slug exists
            $slugExists = $this->db->prepare("SELECT id FROM stores WHERE slug = ?");
            $slugExists->execute([$storeSlug]);
            if ($slugExists->fetch()) {
                return ['success' => false, 'error' => 'Store name already taken'];
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Generate IDs
            $userId = $this->generateId();
            $storeId = $this->generateId();

            // Start transaction
            $this->db->beginTransaction();

            try {
                // Insert user
                $userStmt = $this->db->prepare("
                    INSERT INTO users (id, email, password, name, created_at, updated_at)
                    VALUES (?, ?, ?, ?, datetime('now'), datetime('now'))
                ");
                $userStmt->execute([$userId, $email, $hashedPassword, $name]);

                // Insert store
                $storeStmt = $this->db->prepare("
                    INSERT INTO stores (
                        id, name, slug, description, owner_id, contact_phone,
                        token_balance, plan, is_active, created_at, updated_at
                    )
                    VALUES (?, ?, ?, ?, ?, ?, 50, 'free', 1, datetime('now'), datetime('now'))
                ");
                $storeStmt->execute([$storeId, $storeName, $storeSlug, $storeDescription, $userId, $contactPhone]);

                $this->db->commit();

                return [
                    'success' => true,
                    'userId' => $userId,
                    'storeSlug' => $storeSlug,
                    'message' => 'Registration successful'
                ];
            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function login($email, $password)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                return ['success' => false, 'error' => 'Invalid email or password'];
            }

            // Create session (store hashed token)
            $sessionId = $this->generateId();
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

            $sessionStmt = $this->db->prepare(
                "INSERT INTO sessions (id, user_id, token, expires_at, created_at) VALUES (?, ?, ?, ?, datetime('now'))"
            );
            $sessionStmt->execute([$sessionId, $user['id'], $tokenHash, $expiresAt]);

            // Set session cookie with secure options
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            setcookie('vomp_token', $token, [
                'expires' => strtotime($expiresAt),
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            \Backend\Logger::info('User logged in: ' . $user['id']);

            return [
                'success' => true,
                'userId' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'token' => $token
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getCurrentUser()
    {
        if (!isset($_COOKIE['vomp_token'])) {
            return null;
        }

        $token = $_COOKIE['vomp_token'];
        $tokenHash = hash('sha256', $token);
        $stmt = $this->db->prepare(
            "SELECT users.* FROM users JOIN sessions ON users.id = sessions.user_id WHERE sessions.token = ? AND sessions.expires_at > datetime('now')"
        );
        $stmt->execute([$tokenHash]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function logout()
    {
        if (isset($_COOKIE['vomp_token'])) {
            $tokenHash = hash('sha256', $_COOKIE['vomp_token']);
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE token = ?");
            $stmt->execute([$tokenHash]);
            \Backend\Logger::info('User logged out (token removed)');
        }
        setcookie('vomp_token', '', ['expires' => time() - 3600, 'path' => '/', 'samesite' => 'Lax']);
    }

    private function createSlug($text)
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }

    private function generateId()
    {
        return bin2hex(random_bytes(12));
    }
}
