<?php
/*
 * API endpoint for WhatsApp order redirect.
 * Deducts 1 token from the seller per order, then returns the WhatsApp link.
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Store.php';
require_once __DIR__ . '/../backend/Token.php';

header('Content-Type: application/json');

$slug = $_GET['storeSlug'] ?? null;
$productId = $_GET['productId'] ?? null;
if (!$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'storeSlug required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?: [];

$customer = [
    'name' => $data['name'] ?? '',
    'email' => $data['email'] ?? '',
    'state' => $data['state'] ?? '',
    'delivery_location' => $data['delivery_location'] ?? '',
];

$res = token_deduct_for_order($slug, $productId, $customer);

if (!$res['success']) {
    http_response_code(400);
    echo json_encode($res);
    exit;
}

echo json_encode($res);
