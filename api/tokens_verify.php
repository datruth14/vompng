<?php
/*
 * Paystack payment callback handler.
 * Verifies the transaction and credits tokens to the store.
 * GET /api/tokens_verify.php?reference=REF&storeSlug=SLUG
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Store.php';
require_once __DIR__ . '/../backend/Token.php';
require_once __DIR__ . '/../backend/Paystack.php';
require_once __DIR__ . '/../backend/Logger.php';

$reference = $_GET['reference'] ?? '';

if (!$reference) {
    header('Location: /dashboard?error=' . urlencode('Invalid payment verification link'));
    exit;
}

$storeSlug = $_GET['storeSlug'] ?? '';
$store = null;
if ($storeSlug) {
    $store = store_get_by_slug($storeSlug);
}

$verification = paystack_verify($reference);
if (!$verification['success']) {
    $redirect = $store ? '/dashboard/' . urlencode($storeSlug) . '/tokens' : '/tokens';
    header('Location: ' . $redirect . '?error=' . urlencode($verification['error']));
    exit;
}

$amountPaidKobo = $verification['amount'];
$metadata = $verification['metadata'] ?? [];

$tokens = (int) ($metadata['tokens'] ?? 0);
if ($tokens <= 0) {
    $tokens = (int) ($amountPaidKobo / 100 / TOKEN_PRICE_PER_UNIT);
}

if ($tokens <= 0) {
    $redirect = $store ? '/dashboard/' . urlencode($storeSlug) . '/tokens' : '/tokens';
    header('Location: ' . $redirect . '?error=' . urlencode('Could not determine Vomp Coin count'));
    exit;
}

$userId = (int) ($metadata['user_id'] ?? 0);
if (!$userId && $store) {
    $userId = $store['owner_id'];
}

$result = token_purchase($userId, $tokens, $store ? $store['id'] : null);
if ($result['success']) {
    logger_info("Paystack payment verified: {$reference}, {$tokens} Vomp Coins credited to user {$userId}");
    $redirect = $store ? '/dashboard/' . urlencode($storeSlug) . '/tokens' : '/tokens';
    header('Location: ' . $redirect . '?success=Payment+verified!+' . $tokens . '+Vomp+Coins+added');
} else {
    $redirect = $store ? '/dashboard/' . urlencode($storeSlug) . '/tokens' : '/tokens';
    header('Location: ' . $redirect . '?error=' . urlencode($result['error']));
}
