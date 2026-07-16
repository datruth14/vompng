<?php
require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
header('Content-Type: application/json');

$user = auth_get_current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['score']) || !isset($data['game'])) {
    http_response_code(400);
    echo json_encode(['error' => 'score and game required']);
    exit;
}

$score = intval($data['score']);
$game = trim($data['game']);

if ($score < 0 || $score > 1000000) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid score']);
    exit;
}

$db = db_get_connection();
$stmt = $db->prepare('UPDATE users SET gptokens = gptokens + ? WHERE id = ?');
$stmt->execute([$score, $user['id']]);

$stmt = $db->prepare('SELECT gptokens FROM users WHERE id = ?');
$stmt->execute([$user['id']]);
$newBalance = (int) $stmt->fetchColumn();

echo json_encode([
    'success' => true,
    'score' => $score,
    'newBalance' => $newBalance
]);
