<?php
require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Store.php';

if (!auth_is_admin()) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$storeId = $_POST['store_id'] ?? '';
if (!$storeId) {
    header('Location: /admin/stores?error=Missing store ID');
    exit;
}

$db = db_get_connection();
$stmt = $db->prepare('SELECT is_active FROM stores WHERE id = ?');
$stmt->execute([$storeId]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$store) {
    header('Location: /admin/stores?error=Store not found');
    exit;
}

$newStatus = (int) $store['is_active'] === 1 ? 0 : 1;
$stmt = $db->prepare('UPDATE stores SET is_active = ?, updated_at = NOW() WHERE id = ?');
$stmt->execute([$newStatus, $storeId]);

$msg = $newStatus ? 'Store enabled' : 'Store disabled';
header('Location: /admin/stores?success=' . urlencode($msg));
exit;
