<?php
/*
 * API endpoint to create an additional store for the authenticated user.
 * POST /api/store/create
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Store.php';

header('Content-Type: application/json');

try {
    $currentUser = auth_get_current_user();
    if (!$currentUser) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Not authenticated']);
        exit;
    }

    $raw = file_get_contents('php://input');
    logger_info('store_create raw input: ' . ($raw ?: '(empty)'));
    $input = json_decode($raw, true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        exit;
    }

    $storeName = trim($input['storeName'] ?? '');
    $storeDescription = trim($input['storeDescription'] ?? '');
    $contactPhone = trim($input['contactPhone'] ?? '');
    $contactEmail = trim($input['contactEmail'] ?? '');

    if (empty($storeName) || empty($contactPhone) || empty($contactEmail)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Store name, email and WhatsApp number are required']);
        exit;
    }

    if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        exit;
    }

    if (strlen($storeName) < 2) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Store name must be at least 2 characters']);
        exit;
    }

    // Free users can only create one store
    if (($currentUser['plan'] ?? 'free') !== 'premium') {
        $existingStores = store_get_user_stores($currentUser['id']);
        if (count($existingStores) > 0) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Upgrade to premium to create multiple stores']);
            exit;
        }
    }

    $result = store_create_for_user($currentUser['id'], $storeName, $storeDescription, $contactPhone, $contactEmail);

    if ($result['success']) {
        http_response_code(201);
        echo json_encode(['success' => true, 'storeSlug' => $result['storeSlug'], 'message' => 'Store created successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $result['error']]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
