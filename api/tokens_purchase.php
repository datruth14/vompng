<?php
require_once 'backend/Database.php';
require_once 'backend/Auth.php';
require_once 'backend/Store.php';
require_once 'backend/Token.php';

use Backend\Auth;
use Backend\Store;
use Backend\Token;

header('Content-Type: application/json');

$auth = new Auth();
$currentUser = $auth->getCurrentUser();

if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$storeModel = new Store();
$tokenModel = new Token();

$storeSlug = $_GET['storeSlug'] ?? '';
$store = $storeModel->getStoreBySlugForOwner($storeSlug, $currentUser['id']);

if (!$store) {
    http_response_code(404);
    echo json_encode(['error' => 'Store not found']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $plan = $data['plan'] ?? '';
    if (!$plan) {
        echo json_encode(['success' => false, 'error' => 'Plan is required']);
        exit;
    }

    $result = $tokenModel->purchase($store['id'], $plan);
    echo json_encode($result);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
