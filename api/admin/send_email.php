<?php
require_once __DIR__ . '/../../backend/Database.php';
require_once __DIR__ . '/../../backend/Auth.php';
require_once __DIR__ . '/../../backend/Admin.php';
require_once __DIR__ . '/../../backend/Mailer.php';

header('Content-Type: application/json');

if (!auth_is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'list') {
        $filter = $_GET['filter'] ?? 'all';
        switch ($filter) {
            case 'with_stores':
                $users = admin_get_users_with_stores();
                break;
            case 'without_stores':
                $users = admin_get_users_without_stores();
                break;
            case 'min_2_products':
                $users = admin_get_users_with_min_products(2);
                break;
            case 'min_5_products':
                $users = admin_get_users_with_min_products(5);
                break;
            case 'single':
                $q = $_GET['q'] ?? '';
                if (strlen($q) < 2) {
                    $users = [];
                } else {
                    $like = '%' . $q . '%';
                    $users = db_fetch_all('SELECT id, name, email FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY name ASC LIMIT 20', [$like, $like]);
                }
                break;
            default:
                $users = db_fetch_all('SELECT id, name, email FROM users ORDER BY name ASC');
        }
        echo json_encode(['success' => true, 'users' => $users]);
        exit;
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$recipients = $input['recipients'] ?? [];
$subject = trim($input['subject'] ?? '');
$message = trim($input['message'] ?? '');

if (empty($recipients) || empty($subject) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing recipients, subject, or message']);
    exit;
}

$count = 0;
$errors = [];

foreach ($recipients as $email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email: $email";
        continue;
    }
    $html = '<div style="font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 32px; background: #0a0a0a; color: #fff; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1);">
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="font-size: 28px; font-weight: 900; letter-spacing: 2px; color: #ff610a;">vomp</div>
        </div>
        <div style="font-size: 14px; color: #ddd; line-height: 1.6;">' . nl2br(htmlspecialchars($message)) . '</div>
        <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 24px 0;">
        <p style="font-size: 12px; color: #666; text-align: center;">You received this email because you are registered on vomp.</p>
    </div>';
    $result = mailer_send($email, $subject, $html);
    if ($result['success']) {
        $count++;
    } else {
        $errors[] = "$email: " . ($result['error'] ?? 'unknown error');
    }
    // Small delay to avoid rate limits
    usleep(100000);
}

echo json_encode([
    'success' => true,
    'sent' => $count,
    'total' => count($recipients),
    'errors' => $errors,
]);
