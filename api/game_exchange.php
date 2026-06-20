<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Token.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$userId = $currentUser['id'];
$threshold = 1000000;
$exchangeRate = 50;

$db = db_get_connection();

$stmt = $db->prepare('SELECT gptokens FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || (int) $user['gptokens'] < $threshold) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Insufficient GPTokens. You need at least ' . number_format($threshold) . ' GPTokens to exchange.']);
    exit;
}

$db->beginTransaction();
try {
    $deduct = $db->prepare('UPDATE users SET gptokens = gptokens - ? WHERE id = ? AND gptokens >= ?');
    $deduct->execute([$threshold, $userId, $threshold]);

    if ($deduct->rowCount() === 0) {
        $db->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Insufficient GPTokens.']);
        exit;
    }

    $creditStmt = $db->prepare('UPDATE users SET token_balance = COALESCE(token_balance, 0) + ? WHERE id = ?');
    $creditStmt->execute([$exchangeRate, $userId]);

    $txStmt = $db->prepare("INSERT INTO token_transactions (id, user_id, type, amount, description, created_at) VALUES (?, ?, 'credit', ?, ?, NOW())");
    $txId = bin2hex(random_bytes(12));
    $txStmt->execute([$txId, $userId, $exchangeRate, 'GPToken exchange: ' . number_format($threshold) . ' GPT → ' . $exchangeRate . ' VC']);

    $db->commit();

    $balStmt = $db->prepare('SELECT token_balance, gptokens FROM users WHERE id = ?');
    $balStmt->execute([$userId]);
    $updated = $balStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'GPTokens exchanged successfully',
        'token_balance' => (int) ($updated['token_balance'] ?? 0),
        'gptokens' => (int) ($updated['gptokens'] ?? 0),
    ]);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Exchange failed. Please try again.']);
}
