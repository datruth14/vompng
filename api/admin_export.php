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
               s.visits,
               (SELECT COUNT(*) FROM products p WHERE p.store_id = s.id OR p.store_id = s.owner_id) AS product_count,
               (SELECT COUNT(*) FROM orders o WHERE o.store_id = s.id) AS order_count,
               s.created_at
        FROM stores s
        LEFT JOIN users u ON s.owner_id = u.id
        ORDER BY s.created_at DESC
    ');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="stores_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Store Name', 'Slug', 'Owner', 'Owner Email', 'Phone', 'Email', 'Token Balance', 'Plan', 'Active', 'Visits', 'Orders', 'Products', 'Created']);
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
            (int) ($r['visits'] ?? 0),
            (int) ($r['order_count'] ?? 0),
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
               p.is_available, p.product_condition, p.location, p.affiliate_url, p.created_at
        FROM products p
        JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        ORDER BY p.created_at DESC
    ');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Product', 'Price (₦)', 'Store', 'Category', 'Available', 'Condition', 'Location', 'Type', 'Affiliate URL', 'Created']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['name'],
            number_format((float) $r['price'], 2),
            $r['store_name'] ?? '',
            $r['category'] ?? 'Others',
            (int) ($r['is_available'] ?? 1) === 1 ? 'Yes' : 'No',
            $r['product_condition'] ?? '',
            $r['location'] ?? '',
            !empty($r['affiliate_url']) ? 'Affiliate' : 'Own',
            !empty($r['affiliate_url']) ? $r['affiliate_url'] : '',
            $r['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

if ($type === 'transactions') {
    $rows = db_fetch_all('
        SELECT t.type, t.amount, t.description, s.name AS store_name, t.created_at
        FROM token_transactions t
        JOIN stores s ON t.store_id = s.id
        ORDER BY t.created_at DESC
    ');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="transactions_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Type', 'Amount', 'Description', 'Store', 'Date']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['type'],
            ($r['type'] === 'credit' ? '+' : '-') . abs((int) $r['amount']),
            $r['description'] ?? '',
            $r['store_name'] ?? '',
            $r['created_at'],
        ]);
    }
    fclose($out);
    exit;
}

if ($type === 'bill_payments') {
    require_once __DIR__ . '/../backend/BillPayment.php';
    $rows = db_fetch_all('
        SELECT u.name AS user_name, u.email AS user_email,
               bp.type, bp.service_id, bp.customer_id,
               bp.amount_naira, bp.commission, bp.coins_deducted,
               bp.provider_ref, bp.status, bp.created_at
        FROM bill_payments bp
        LEFT JOIN users u ON bp.user_id = u.id
        ORDER BY bp.created_at DESC
    ');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="bill_payments_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['User', 'Email', 'Type', 'Service', 'Customer', 'NGN Amount', 'Commission (' . BILL_COMMISSION_PERCENT . '%)', 'VC Used', 'Provider Ref', 'Status', 'Date']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['user_name'] ?? '',
            $r['user_email'] ?? '',
            $r['type'] ?? '',
            $r['service_id'] ?? '',
            $r['customer_id'] ?? '',
            number_format((float) ($r['amount_naira'] ?? 0), 2),
            number_format((float) ($r['commission'] ?? 0), 2),
            (int) ($r['coins_deducted'] ?? 0),
            $r['provider_ref'] ?? '',
            $r['status'] ?? '',
            $r['created_at'] ?? '',
        ]);
    }
    fclose($out);
    exit;
}

if ($type === 'withdrawals') {
    $rows = db_fetch_all('
        SELECT u.name AS user_name, u.email AS user_email,
               w.amount, w.naira_amount, w.bank_name, w.account_number, w.account_name,
               w.status, w.created_at
        FROM withdrawals w
        LEFT JOIN users u ON w.user_id = u.id
        ORDER BY w.created_at DESC
    ');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="withdrawals_export_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['User', 'Email', 'Tokens', 'NGN Amount', 'Platform Commission (2%)', 'Bank', 'Account Number', 'Account Name', 'Status', 'Date']);
    foreach ($rows as $r) {
        $commission = (int) ($r['naira_amount'] * 0.02);
        fputcsv($out, [
            $r['user_name'] ?? '',
            $r['user_email'] ?? '',
            (int) ($r['amount'] ?? 0),
            number_format((int) ($r['naira_amount'] ?? 0)),
            number_format($commission),
            $r['bank_name'] ?? '',
            $r['account_number'] ?? '',
            $r['account_name'] ?? '',
            $r['status'] ?? '',
            $r['created_at'] ?? '',
        ]);
    }
    fclose($out);
    exit;
}

http_response_code(400);
echo 'Invalid export type';
