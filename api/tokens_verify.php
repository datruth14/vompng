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
$storeSlug = $_GET['storeSlug'] ?? '';

if (!$reference || !$storeSlug) {
    header('Location: /dashboard?error=' . urlencode('Invalid payment verification link'));
    exit;
}

$store = store_get_by_slug($storeSlug);
if (!$store) {
    header('Location: /dashboard?error=' . urlencode('Store not found'));
    exit;
}

$verification = paystack_verify($reference);
if (!$verification['success']) {
    header('Location: /dashboard/' . urlencode($storeSlug) . '/tokens?error=' . urlencode($verification['error']));
    exit;
}

$amountPaidKobo = $verification['amount'];
$metadata = $verification['metadata'] ?? [];

// Calculate tokens from metadata or amount
$tokens = (int) ($metadata['tokens'] ?? 0);
if ($tokens <= 0) {
    $tokens = (int) ($amountPaidKobo / 100 / TOKEN_PRICE_PER_UNIT);
}

if ($tokens <= 0) {
    header('Location: /dashboard/' . urlencode($storeSlug) . '/tokens?error=' . urlencode('Could not determine token count'));
    exit;
}

$result = token_purchase($store['id'], $tokens);
if ($result['success']) {
    logger_info("Paystack payment verified: {$reference}, {$tokens} tokens credited to store {$storeSlug}");
    header('Location: /dashboard/' . urlencode($storeSlug) . '/tokens?success=Payment+verified!+' . $tokens . '+tokens+added');
} else {
    header('Location: /dashboard/' . urlencode($storeSlug) . '/tokens?error=' . urlencode($result['error']));
}
