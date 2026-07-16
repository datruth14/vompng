<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$bankName = trim($data['bank_name'] ?? '');
$bankCode = trim($data['bank_code'] ?? '');
$accountNumber = trim($data['account_number'] ?? '');
$accountName = trim($data['account_name'] ?? '');

if (!$bankName || !$bankCode || !$accountNumber || !$accountName) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'All bank details are required']);
    exit;
}

if (!preg_match('/^\d{10}$/', $accountNumber)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid account number (must be 10 digits)']);
    exit;
}

$db = db_get_connection();
$stmt = $db->prepare('UPDATE users SET bank_name = ?, bank_account_number = ?, bank_account_name = ? WHERE id = ?');
$stmt->execute([$bankName, $accountNumber, $accountName, $currentUser['id']]);

echo json_encode(['success' => true, 'message' => 'Bank details saved']);
