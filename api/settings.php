<?php
require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Store.php';

use Backend\Auth;
use Backend\Store;

header('Content-Type: application/json');

$auth = new Auth();
$user = $auth->getCurrentUser();
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$slug = $_GET['storeSlug'] ?? null;
if (!$slug) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'storeSlug required']);
    exit;
}

$storeModel = new Store();
$store = $storeModel->getStoreBySlugForOwner($slug, $user['id']);
if (!$store) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Store not found or not owned by you']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode(['success' => true, 'store' => $store]);
    exit;
}

if ($method === 'POST') {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON body']);
        exit;
    }

    // Only allow a whitelist of fields
    $allowed = [
        'name', 'description', 'contact_phone', 'contact_email',
        'logo_url', 'hero_image_url', 'hero_color', 'accent_color', 'is_active'
    ];

    $toUpdate = [];
    foreach ($allowed as $k) {
        if (array_key_exists($k, $data)) {
            $toUpdate[$k] = $data[$k];
        }
    }

    $res = $storeModel->updateStore($store['id'], $toUpdate);
    if ($res['success']) {
        echo json_encode($res);
    } else {
        http_response_code(400);
        echo json_encode($res);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
