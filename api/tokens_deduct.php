<?php
require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Token.php';

use Backend\Token;

header('Content-Type: application/json');

$slug = $_GET['storeSlug'] ?? null;
if (!$slug) {
    http_response_code(400);
    echo json_encode(['error' => 'storeSlug required']);
    exit;
}

$token = new Token();
$res = $token->deductForOrder($slug);

if (!$res['success']) {
    if (isset($res['code']) && $res['code'] === 'NO_TOKENS') {
        http_response_code(402);
    } else {
        http_response_code(400);
    }
    echo json_encode($res);
    exit;
}

echo json_encode($res);
