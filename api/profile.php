<?php
require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Logger.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request body']);
    exit;
}

// Verify current password if a new password is being set
if (!empty($input['password'])) {
    $currentPassword = $input['current_password'] ?? '';
    if (empty($currentPassword)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Current password is required to set a new password']);
        exit;
    }
    if (!password_verify($currentPassword, $currentUser['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
        exit;
    }
}

$result = auth_update_user($currentUser['id'], $input);
echo json_encode($result);