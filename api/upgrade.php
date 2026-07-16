<?php
/*
 * API endpoint to upgrade a user to premium by deducting 500 Vomp Coins.
 * POST /api/upgrade
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Token.php';
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
    $input = json_decode($raw, true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }

    $storeSlug = $input['storeSlug'] ?? '';
    $storeId = null;
    if ($storeSlug) {
        $store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);
        $storeId = $store ? $store['id'] : null;
    }

    $result = token_upgrade_to_premium($currentUser['id'], $storeId);
    echo json_encode($result);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
