<?php
/*
 * Export orders for a store as CSV.
 * GET /api/orders_export.php?storeSlug=X&from=Y&to=Z
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Store.php';
require_once __DIR__ . '/../backend/Order.php';

$slug = $_GET['storeSlug'] ?? null;
if (!$slug) {
    http_response_code(400);
    echo 'storeSlug required';
    exit;
}

// Auth check
$user = auth_get_current_user();
if (!$user) {
    http_response_code(401);
    echo 'Unauthorized';
    exit;
}

$store = store_get_by_slug_for_owner($slug, $user['id']);
if (!$store) {
    http_response_code(404);
    echo 'Store not found';
    exit;
}

$from = $_GET['from'] ?? null;
$to = $_GET['to'] ?? null;
$orders = order_get_by_store_all($store['id'], $from, $to);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="orders-' . htmlspecialchars($slug) . '-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Customer Name', 'Email', 'Product', 'State', 'Delivery Location', 'Date']);

foreach ($orders as $ord) {
    fputcsv($out, [
        $ord['customer_name'],
        $ord['customer_email'],
        $ord['product_name'],
        $ord['state'],
        $ord['delivery_location'],
        $ord['created_at'],
    ]);
}

fclose($out);
