<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Token.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$amount = (int) ($data['amount'] ?? 0);

if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Recipient email is required']);
    exit;
}

if ($amount < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Amount must be at least 1 Vomp Coin']);
    exit;
}

$result = token_transfer($currentUser['id'], $email, $amount);
echo json_encode($result);
