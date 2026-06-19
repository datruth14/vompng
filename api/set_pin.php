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
$pin = trim($data['pin'] ?? '');
$currentPin = $data['current_pin'] ?? '';

if (!$pin || !preg_match('/^\d{4}$/', $pin)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'PIN must be exactly 4 digits']);
    exit;
}

$db = db_get_connection();

if (!empty($currentUser['transaction_pin'])) {
    if (!$currentPin) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Current PIN is required to change it']);
        exit;
    }
    if (!password_verify($currentPin, $currentUser['transaction_pin'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Current PIN is incorrect']);
        exit;
    }
}

$hash = password_hash($pin, PASSWORD_BCRYPT);
$stmt = $db->prepare('UPDATE users SET transaction_pin = ? WHERE id = ?');
$stmt->execute([$hash, $currentUser['id']]);

echo json_encode(['success' => true, 'message' => 'Transaction PIN set successfully']);
