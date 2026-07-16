<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Paystack.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$banks = paystack_list_banks();
$result = [];
foreach ($banks as $b) {
    $result[] = [
        'code' => $b['code'],
        'name' => $b['name'],
    ];
}

echo json_encode(['success' => true, 'banks' => $result]);
