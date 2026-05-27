<?php
require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Admin.php';

if (!auth_is_admin()) {
    http_response_code(403);
    exit;
}

$type = $_GET['type'] ?? '';

if ($type === 'users') {
    $rows = db_fetch_all('
        SELECT u.name, u.email, u.phone, u.token_balance, u.plan, u.role,
               (SELECT COUNT(*) FROM stores WHERE owner_id = u.id) AS store_count,
               u.created_at
        FROM users u
        ORDER BY u.created_at DESC
    ');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Name', 'Email', 'Phone', 'Token Balance', 'Plan', 'Role', 'Stores', 'Joined']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['name'],
            $r['email'],
            $r['phone'] ?? '',
            (int) ($r['token_balance'] ?? 0),
            $r['plan'] ?? 'free',
            $r['role'] ?? 'user',
            (int) ($r['store_count'] ?? 0),
            $r['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

if ($type === 'stores') {
    $rows = db_fetch_all('
        SELECT s.name, s.slug, u.name AS owner_name, u.email AS owner_email,
               s.contact_phone, s.contact_email,
               s.token_balance, s.plan, s.is_active,
               (SELECT COUNT(*) FROM products p WHERE p.store_id = s.id OR p.store_id = s.owner_id) AS product_count,
               s.created_at
        FROM stores s
        LEFT JOIN users u ON s.owner_id = u.id
        ORDER BY s.created_at DESC
    ');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="stores_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Store Name', 'Slug', 'Owner', 'Owner Email', 'Phone', 'Email', 'Token Balance', 'Plan', 'Active', 'Products', 'Created']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['name'],
            $r['slug'],
            $r['owner_name'] ?? '',
            $r['owner_email'] ?? '',
            $r['contact_phone'] ?? '',
            $r['contact_email'] ?? '',
            (int) ($r['token_balance'] ?? 0),
            $r['plan'] ?? 'free',
            (int) ($r['is_active'] ?? 1) === 1 ? 'Yes' : 'No',
            (int) ($r['product_count'] ?? 0),
            $r['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

if ($type === 'products') {
    $rows = db_fetch_all('
        SELECT p.name, p.price, s.name AS store_name, p.category,
               p.is_available, p.product_condition, p.location, p.created_at
        FROM products p
        JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        ORDER BY p.created_at DESC
    ');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Product', 'Price (₦)', 'Store', 'Category', 'Available', 'Condition', 'Location', 'Created']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['name'],
            number_format((float) $r['price'], 2),
            $r['store_name'] ?? '',
            $r['category'] ?? 'Others',
            (int) ($r['is_available'] ?? 1) === 1 ? 'Yes' : 'No',
            $r['product_condition'] ?? '',
            $r['location'] ?? '',
            $r['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

http_response_code(400);
echo 'Invalid export type';
