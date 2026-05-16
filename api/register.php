<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';

use Backend\Auth;

$auth = new Auth();
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$result = $auth->register(
    $input['name'] ?? '',
    $input['email'] ?? '',
    $input['password'] ?? '',
    $input['storeName'] ?? '',
    $input['storeDescription'] ?? '',
    $input['contactPhone'] ?? ''
);

// Return JSON only for API clients; browser form posts get redirects.
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
$wantsJson = stripos($accept, 'application/json') !== false;

if ($result['success']) {
    if ($wantsJson) {
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        header('Location: /login?registered=1');
    }
    exit;
}

if ($wantsJson) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode($result);
    exit;
}

$error = urlencode($result['error'] ?? 'Registration failed');
header("Location: /register?error={$error}");
exit;
