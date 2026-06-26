<?php
require_once __DIR__ . '/../../backend/Database.php';
require_once __DIR__ . '/../../backend/Auth.php';

header('Content-Type: application/json');

if (!auth_is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = $input['user_id'] ?? '';
$password = $input['password'] ?? '';

if (empty($userId) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing user_id or password']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}

$result = auth_update_user($userId, ['password' => $password]);

if ($result['success']) {
    echo json_encode(['success' => true, 'message' => 'Password reset successful']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Failed to reset password']);
}
