<?php
/*
 * API endpoint for WhatsApp order redirect.
 * Generates the WhatsApp link and stores the order in the database.
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Store.php';
require_once __DIR__ . '/../backend/Product.php';
require_once __DIR__ . '/../backend/Token.php';
require_once __DIR__ . '/../backend/Order.php';

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

// Store the order
$store = store_get_by_slug($slug);
if ($store) {
    $productName = '';
    if ($productId) {
        $product = product_get_by_id_and_store($productId, $store['id']);
        $productName = $product ? $product['name'] : '';
    }
    order_create($store['id'], $productId, $productName, $data['name'] ?? '', $data['email'] ?? '', $data['state'] ?? '', $data['delivery_location'] ?? '');
}

echo json_encode($res);
