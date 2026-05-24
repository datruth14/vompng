<?php
/*
 * Token helper functions.
 * Handles token plan listing, purchase workflows, product upload deductions, and transaction history.
 * Tokens are deducted when a seller uploads a product, not when a buyer clicks the order button.
 */


require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Product.php';

const TOKEN_PRICE_PER_UNIT = 20;
const TOKEN_MINIMUM = 50;

/* Get user's current token balance. */

function token_user_balance($userId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT token_balance FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int) $row['token_balance'] : 0;
}

/* Purchase tokens for a user (central balance). Logs transaction under the given store. */

function token_purchase($userId, $tokenCount, $logStoreId = null)
{
    $cost = TOKEN_PRICE_PER_UNIT;
    $min = TOKEN_MINIMUM;

    $tokenCount = (int) $tokenCount;
    if ($tokenCount < $min) {
        return ['success' => false, 'error' => "Minimum purchase is {$min} Vomp Coins (₦" . number_format($min * $cost) . ")"];
    }

    $totalAmount = $tokenCount * $cost;
    $db = db_get_connection();

    $userStmt = $db->prepare('SELECT token_balance FROM users WHERE id = ?');
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return ['success' => false, 'error' => 'User not found'];
    }

    $newBalance = ((int) $user['token_balance']) + $tokenCount;
    $db->beginTransaction();

    try {
        $update = $db->prepare("UPDATE users SET token_balance = ?, plan = 'premium' WHERE id = ?");
        $update->execute([$newBalance, $userId]);

        if ($logStoreId) {
            $log = $db->prepare('INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, \'credit\', ?, ?, NOW())');
            $log->execute([
                bin2hex(random_bytes(12)),
                $logStoreId,
                $tokenCount,
                "Purchased {$tokenCount} Vomp Coins (₦" . number_format($totalAmount) . ")",
            ]);
        }

        $db->commit();
        return ['success' => true, 'token_balance' => $newBalance, 'added' => $tokenCount];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'error' => 'Failed to complete Vomp Coin purchase'];
    }
}

/* Build the WhatsApp redirect URL for an order. Deducts 1 token from the store owner's central balance. */

function token_deduct_for_order($slug, $productId = null, $customer = [])
{
    $db = db_get_connection();
    $storeStmt = $db->prepare('SELECT s.id, s.contact_phone, s.name, s.owner_id FROM stores s WHERE s.slug = ? AND s.is_active = 1');
    $storeStmt->execute([$slug]);
    $store = $storeStmt->fetch(PDO::FETCH_ASSOC);

    if (!$store) {
        return ['success' => false, 'error' => 'Store not found'];
    }

    // Check owner's central token balance
    $userStmt = $db->prepare('SELECT token_balance FROM users WHERE id = ?');
    $userStmt->execute([$store['owner_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || (int) $user['token_balance'] < 1) {
        return ['success' => false, 'error' => 'Seller has insufficient Vomp Coins to receive orders. Please contact the seller.', 'code' => 'NO_TOKENS'];
    }

    $productName = '';
    $productPrice = '';
    $productUrl = '';
    if ($productId) {
        $product = product_get_by_id_and_store($productId, $store['id']);
        if (!$product) {
            return ['success' => false, 'error' => 'Product not found'];
        }
        $productName = $product['name'];
        $productPrice = '₦' . number_format((float) $product['price'], 2);

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
        $productUrl = "{$scheme}://{$host}/store/{$slug}/{$productId}";
    }

    $number = preg_replace('/\D+/', '', (string) ($store['contact_phone'] ?? ''));

    if ($number === '') {
        return ['success' => false, 'error' => 'Store has no WhatsApp number configured'];
    }

    if (!str_starts_with($number, '234')) {
        $number = '234' . $number;
    }

    // Deduct 1 token from user's central balance
    $newBalance = (int) $user['token_balance'] - 1;
    $db->beginTransaction();
    try {
        $update = $db->prepare('UPDATE users SET token_balance = ? WHERE id = ? AND token_balance >= 1');
        $update->execute([$newBalance, $store['owner_id']]);

        if ($update->rowCount() === 0) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Seller has insufficient Vomp Coins to receive orders.', 'code' => 'NO_TOKENS'];
        }

        $log = $db->prepare('INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, \'debit\', 1, ?, NOW())');
        $log->execute([
            bin2hex(random_bytes(12)),
            $store['id'],
            'Order via WhatsApp',
        ]);

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'error' => 'Failed to process order'];
    }

    $customerName = $customer['name'] ?? 'A buyer';

    $lines = [];
    $lines[] = "🛒 *New Order — {$store['name']}*";
    $lines[] = "";

    if ($productName) {
        $lines[] = "My name is {$customerName}, I'm interested in \"{$productName}\" priced at {$productPrice} listed on your Vomp store.";
    } else {
        $lines[] = "My name is {$customerName}, I'm interested in items from your Vomp store.";
    }

    $lines[] = "";
    $lines[] = "*My Details:*";
    $lines[] = "• *Email:* " . ($customer['email'] ?? 'Not provided');
    $lines[] = "• *State:* " . ($customer['state'] ?? 'Not provided');
    $lines[] = "• *Delivery Location:* " . ($customer['delivery_location'] ?? 'Not provided');

    if ($productUrl) {
        $lines[] = "";
        $lines[] = $productUrl;
    }

    $message = rawurlencode(implode("\n", $lines));
    $waUrl = "https://wa.me/{$number}?text={$message}";

    return ['success' => true, 'whatsappUrl' => $waUrl, 'token_balance' => $newBalance];
}

/* Deduct 10 tokens from the user's central balance when uploading a product. */

function token_deduct_for_product_upload($userId, $storeIdForLog = null)
{
    $db = db_get_connection();
    $cost = 10;

    $userStmt = $db->prepare('SELECT token_balance FROM users WHERE id = ?');
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return ['success' => false, 'error' => 'User not found'];
    }

    if ((int) $user['token_balance'] < $cost) {
        return ['success' => false, 'error' => 'Insufficient Vomp Coins to publish a product. You need at least 10.', 'code' => 'NO_TOKENS'];
    }

    $newBalance = ((int) $user['token_balance']) - $cost;
    $storeIdForLog = $storeIdForLog ?: '';

    $db->beginTransaction();
    try {
        $update = $db->prepare('UPDATE users SET token_balance = ? WHERE id = ? AND token_balance >= ?');
        $update->execute([$newBalance, $userId, $cost]);

        if ($update->rowCount() === 0) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Insufficient Vomp Coins to publish a product.', 'code' => 'NO_TOKENS'];
        }

        if ($storeIdForLog) {
            $log = $db->prepare('INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, \'debit\', ?, ?, NOW())');
            $log->execute([
                bin2hex(random_bytes(12)),
                $storeIdForLog,
                $cost,
                'Product listing published (10 Vomp Coins)',
            ]);
        }

        $db->commit();
        return ['success' => true, 'token_balance' => $newBalance];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'error' => 'Failed to deduct Vomp Coins for product upload'];
    }
}

/* Return the most recent token transaction history for a store. */

function token_history($storeId, $limit = 50)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM token_transactions WHERE store_id = ? ORDER BY created_at DESC LIMIT ?');
    $stmt->bindValue(1, $storeId);
    $stmt->bindValue(2, (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Return token transactions filtered by date range. */

function token_history_by_date($storeId, $from, $to, $limit = 200)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM token_transactions WHERE store_id = ? AND DATE(created_at) >= ? AND DATE(created_at) <= ? ORDER BY created_at DESC LIMIT ?');
    $stmt->bindValue(1, $storeId);
    $stmt->bindValue(2, $from);
    $stmt->bindValue(3, $to);
    $stmt->bindValue(4, (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Upgrade a user to premium by deducting 500 Vomp Coins from their balance. */

function token_upgrade_to_premium($userId, $logStoreId = null)
{
    $db = db_get_connection();
    $cost = 500;

    $userStmt = $db->prepare('SELECT token_balance, plan FROM users WHERE id = ?');
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return ['success' => false, 'error' => 'User not found'];
    }

    if ($user['plan'] === 'premium') {
        return ['success' => false, 'error' => 'You are already on the premium plan'];
    }

    if ((int) $user['token_balance'] < $cost) {
        return ['success' => false, 'error' => "Insufficient Vomp Coins. Premium upgrade costs {$cost} Vomp Coins.", 'code' => 'NO_TOKENS'];
    }

    $newBalance = (int) $user['token_balance'] - $cost;

    $db->beginTransaction();
    try {
        $update = $db->prepare("UPDATE users SET token_balance = ?, plan = 'premium' WHERE id = ? AND token_balance >= ?");
        $update->execute([$newBalance, $userId, $cost]);

        if ($update->rowCount() === 0) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Failed to upgrade. Insufficient Vomp Coins.', 'code' => 'NO_TOKENS'];
        }

        if ($logStoreId) {
            $log = $db->prepare('INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, \'debit\', ?, ?, NOW())');
            $log->execute([
                bin2hex(random_bytes(12)),
                $logStoreId,
                $cost,
                'Premium plan upgrade (500 Vomp Coins)',
            ]);
        }

        $db->commit();
        return ['success' => true, 'plan' => 'premium', 'token_balance' => $newBalance];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'error' => 'Failed to upgrade to premium'];
    }
}
