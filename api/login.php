<?php
/*
 * API endpoint for user login.
 * Authenticates credentials and issues a session token cookie.
 */


require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$result = auth_login(
    $input['email'] ?? '',
    $input['password'] ?? ''
);

$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
$wantsJson = stripos($accept, 'application/json') !== false;

if ($result['success']) {
    if ($wantsJson) {
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        header('Location: /dashboard');
    }
    exit;
}

if ($wantsJson) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode($result);
    exit;
}

$error = urlencode($result['error'] ?? 'Login failed');
header("Location: /login?error={$error}");
exit;
