<?php
/*
 * API endpoint to initialize Paystack payment for token purchases.
 * POST /api/tokens/purchase
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Store.php';
require_once __DIR__ . '/../backend/Token.php';
require_once __DIR__ . '/../backend/Paystack.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$tokens = isset($data['tokens']) ? (int) $data['tokens'] : 0;
$pin = $data['pin'] ?? '';

if (!$pin) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Transaction PIN is required']);
    exit;
}

$pinCheck = auth_verify_transaction_pin($currentUser['id'], $pin);
if (!$pinCheck['success']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $pinCheck['error']]);
    exit;
}

if ($tokens < TOKEN_MINIMUM) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => "Minimum purchase is " . TOKEN_MINIMUM . " Vomp Coins"]);
    exit;
}

$storeSlug = $_GET['storeSlug'] ?? '';
$store = null;
if ($storeSlug) {
    $store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);
}

$totalAmountKobo = $tokens * TOKEN_PRICE_PER_UNIT * 100;

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$callbackUrl = "{$scheme}://{$host}/api/tokens_verify.php";
if ($store) {
    $callbackUrl .= "?storeSlug={$storeSlug}";
}

$metadata = [
    'user_id' => $currentUser['id'],
    'tokens' => $tokens,
];
if ($store) {
    $metadata['store_slug'] = $storeSlug;
    $metadata['store_id'] = $store['id'];
}

$result = paystack_initialize(
    $currentUser['email'],
    $totalAmountKobo,
    $metadata,
    $callbackUrl
);

echo json_encode($result);
