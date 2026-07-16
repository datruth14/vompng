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

if (!$pin) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'PIN is required']);
    exit;
}

if (empty($currentUser['transaction_pin'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No transaction PIN set', 'setup_required' => true]);
    exit;
}

if (!password_verify($pin, $currentUser['transaction_pin'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid transaction PIN']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'PIN verified']);
