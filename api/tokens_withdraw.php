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
$amount = (int) ($data['amount'] ?? 0);
$bankName = trim($data['bank_name'] ?? '');
$accountNumber = trim($data['account_number'] ?? '');
$accountName = trim($data['account_name'] ?? '');

if ($amount < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Amount must be at least 1 Vomp Coin']);
    exit;
}

if (!$bankName || !$accountNumber || !$accountName) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'All bank details are required']);
    exit;
}

if (!preg_match('/^\d{10}$/', $accountNumber)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid account number (must be 10 digits)']);
    exit;
}

$result = token_withdraw($currentUser['id'], $amount, $bankName, $accountNumber, $accountName);
echo json_encode($result);
