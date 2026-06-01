<?php
/*
 * API endpoint for user registration.
 * Accepts registration payloads and creates a new user + store.
 */

ob_start();
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../backend/Database.php';
    require_once __DIR__ . '/../backend/Auth.php';

    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if (!$input) {
        ob_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request data']);
        exit;
    }

    $result = auth_register(
        $input['name'] ?? '',
        $input['email'] ?? '',
        $input['password'] ?? '',
        $input['storeName'] ?? '',
        $input['storeDescription'] ?? '',
        $input['contactPhone'] ?? '',
        $input['phone'] ?? ''
    );

    $stray = ob_get_clean();
    if ($stray) {
        error_log('register.php stray output: ' . substr($stray, 0, 500));
    }
    if ($result['success']) {
        http_response_code(201);
        echo json_encode($result);
        exit;
    }

    http_response_code(400);
    echo json_encode($result);
    exit;
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    exit;
}
