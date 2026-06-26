<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Store.php';
require_once __DIR__ . '/../backend/Product.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$productId = $data['product_id'] ?? '';
$storeSlug = $data['store_slug'] ?? '';

if (!$productId || !$storeSlug) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing product_id or store_slug']);
    exit;
}

$store = store_get_by_slug($storeSlug);
if (!$store) {
    http_response_code(404);
    echo json_encode(['error' => 'Store not found']);
    exit;
}

$product = product_get_by_id_and_store($productId, $store['id']);
if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit;
}

$db = db_get_connection();
$orderId = bin2hex(random_bytes(12));
$stmt = $db->prepare('INSERT INTO orders (id, store_id, product_id, product_name, customer_name, customer_email, state, delivery_location, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
$stmt->execute([
    $orderId,
    $store['id'],
    $productId,
    $product['name'],
    'Affiliate Visitor',
    '',
    '',
    ''
]);

echo json_encode(['success' => true]);
