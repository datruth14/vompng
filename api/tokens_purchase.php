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

$storeSlug = $_GET['storeSlug'] ?? '';
$store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);
if (!$store) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Store not found']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$tokens = isset($data['tokens']) ? (int) $data['tokens'] : 0;

if ($tokens < TOKEN_MINIMUM) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => "Minimum purchase is " . TOKEN_MINIMUM . " tokens"]);
    exit;
}

$totalAmountKobo = $tokens * TOKEN_PRICE_PER_UNIT * 100; // Paystack uses kobo (NGN * 100)

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$callbackUrl = "{$scheme}://{$host}/api/tokens_verify.php?storeSlug={$storeSlug}";

$result = paystack_initialize(
    $currentUser['email'],
    $totalAmountKobo,
    [
        'store_slug' => $storeSlug,
        'store_id' => $store['id'],
        'user_id' => $currentUser['id'],
        'tokens' => $tokens,
    ],
    $callbackUrl
);

echo json_encode($result);
