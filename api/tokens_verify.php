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
$rawMetadata = $verification['metadata'] ?? [];

// Normalize metadata: Paystack may return it as array or JSON-encoded string
$metadata = is_array($rawMetadata) ? $rawMetadata : (json_decode($rawMetadata, true) ?: []);
// Also check nested custom_fields
if (empty($metadata['user_id']) && !empty($metadata['custom_fields'])) {
    foreach ($metadata['custom_fields'] as $cf) {
        if (isset($cf['variable_name']) && $cf['variable_name'] === 'user_id') {
            $metadata['user_id'] = $cf['value'] ?? 0;
        }
    }
}

logger_info("Paystack verify metadata: " . json_encode($metadata));

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

// Fallback: get user from active session
if (!$userId) {
    $currentUser = auth_get_current_user();
    if ($currentUser) {
        $userId = $currentUser['id'];
    }
}

if (!$userId) {
    $redirect = $store ? '/dashboard/' . urlencode($storeSlug) . '/tokens' : '/tokens';
    header('Location: ' . $redirect . '?error=' . urlencode('Could not identify your account. Please contact support.'));
    exit;
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
