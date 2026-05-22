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

/* Purchase tokens with a custom amount. */

function token_purchase($storeId, $tokenCount)
{
    $cost = TOKEN_PRICE_PER_UNIT;
    $min = TOKEN_MINIMUM;

    $tokenCount = (int) $tokenCount;
    if ($tokenCount < $min) {
        return ['success' => false, 'error' => "Minimum purchase is {$min} tokens (₦" . number_format($min * $cost) . ")"];
    }

    $totalAmount = $tokenCount * $cost;
    $db = db_get_connection();

    $storeStmt = $db->prepare('SELECT token_balance FROM stores WHERE id = ?');
    $storeStmt->execute([$storeId]);
    $store = $storeStmt->fetch(PDO::FETCH_ASSOC);

    if (!$store) {
        return ['success' => false, 'error' => 'Store not found'];
    }

    $newBalance = ((int) $store['token_balance']) + $tokenCount;
    $db->beginTransaction();

    try {
        $update = $db->prepare('UPDATE stores SET token_balance = ?, plan = \'premium\', updated_at = datetime(\'now\') WHERE id = ?');
        $update->execute([$newBalance, $storeId]);

        $log = $db->prepare('INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, \'credit\', ?, ?, datetime(\'now\'))');
        $log->execute([
            bin2hex(random_bytes(12)),
            $storeId,
            $tokenCount,
            "Purchased {$tokenCount} tokens (₦" . number_format($totalAmount) . ")",
        ]);

        $db->commit();
        return ['success' => true, 'token_balance' => $newBalance, 'added' => $tokenCount];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'error' => 'Failed to complete token purchase'];
    }
}

/* Build the WhatsApp redirect URL for an order. Deducts 1 token per order. */

function token_deduct_for_order($slug, $productId = null, $customer = [])
{
    $db = db_get_connection();
    $storeStmt = $db->prepare('SELECT id, contact_phone, name, token_balance FROM stores WHERE slug = ? AND is_active = 1');
    $storeStmt->execute([$slug]);
    $store = $storeStmt->fetch(PDO::FETCH_ASSOC);

    if (!$store) {
        return ['success' => false, 'error' => 'Store not found'];
    }

    if ((int) $store['token_balance'] < 1) {
        return ['success' => false, 'error' => 'Seller has insufficient tokens to receive orders. Please contact the seller.', 'code' => 'NO_TOKENS'];
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

    // Deduct 1 token
    $newBalance = (int) $store['token_balance'] - 1;
    $db->beginTransaction();
    try {
        $update = $db->prepare('UPDATE stores SET token_balance = ?, updated_at = datetime(\'now\') WHERE id = ? AND token_balance >= 1');
        $update->execute([$newBalance, $store['id']]);

        if ($update->rowCount() === 0) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Seller has insufficient tokens to receive orders.', 'code' => 'NO_TOKENS'];
        }

        $log = $db->prepare('INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, \'debit\', 1, ?, datetime(\'now\'))');
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

/* Deduct 1 token when a seller uploads a new product. */

function token_deduct_for_product_upload($storeId)
{
    $db = db_get_connection();
    $storeStmt = $db->prepare('SELECT token_balance FROM stores WHERE id = ?');
    $storeStmt->execute([$storeId]);
    $store = $storeStmt->fetch(PDO::FETCH_ASSOC);

    if (!$store) {
        return ['success' => false, 'error' => 'Store not found'];
    }

    $cost = 10;

    if ((int) $store['token_balance'] < $cost) {
        return ['success' => false, 'error' => 'Insufficient tokens to publish a product. You need at least 10 tokens.', 'code' => 'NO_TOKENS'];
    }

    $newBalance = ((int) $store['token_balance']) - $cost;

    $db->beginTransaction();
    try {
        $update = $db->prepare('UPDATE stores SET token_balance = ?, updated_at = datetime(\'now\') WHERE id = ? AND token_balance >= ?');
        $update->execute([$newBalance, $storeId, $cost]);

        if ($update->rowCount() === 0) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Insufficient tokens to publish a product.', 'code' => 'NO_TOKENS'];
        }

        $log = $db->prepare('INSERT INTO token_transactions (id, store_id, type, amount, description, created_at) VALUES (?, ?, \'debit\', ?, ?, datetime(\'now\'))');
        $log->execute([
            bin2hex(random_bytes(12)),
            $storeId,
            $cost,
            'Product listing published (10 tokens)',
        ]);

        $db->commit();
        return ['success' => true, 'token_balance' => $newBalance];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'error' => 'Failed to deduct token for product upload'];
    }
}

/* Return the most recent token transaction history for a store. */

function token_history($storeId, $limit = 50)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM token_transactions WHERE store_id = ? ORDER BY created_at DESC LIMIT ?');
    $stmt->execute([$storeId, (int) $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Return token transactions filtered by date range. */

function token_history_by_date($storeId, $from, $to, $limit = 200)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM token_transactions WHERE store_id = ? AND date(created_at) >= ? AND date(created_at) <= ? ORDER BY created_at DESC LIMIT ?');
    $stmt->execute([$storeId, $from, $to, (int) $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
