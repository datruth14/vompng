<?php
// VomP adapter — replaces old app header.php
// Provides auth, score save, and user data for game scripts
require_once __DIR__ . '/../../backend/Database.php';
require_once __DIR__ . '/../../backend/Auth.php';

$gpad_user = auth_get_current_user();
if (!$gpad_user) {
    header('Location: /login');
    exit;
}

// Determine game name from the calling file
$gpad_game = 'unknown';
$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
if (isset($trace[0]['file'])) {
    $gpad_game = strtolower(pathinfo($trace[0]['file'], PATHINFO_FILENAME));
}

// Handle score save form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveData'])) {
    $scoreValueData = (int)($_POST['scoreValueData'] ?? 0);
    if ($scoreValueData >= 1000) {
        $db = db_get_connection();
        $db->exec("CREATE TABLE IF NOT EXISTS game_scores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id VARCHAR(255) NOT NULL,
            game VARCHAR(100) DEFAULT 'colorswipe',
            score INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $stmt = $db->prepare("INSERT INTO game_scores (user_id, game, score) VALUES (?, ?, ?)");
        $stmt->execute([$gpad_user['id'], $gpad_game, $scoreValueData]);
        // Add to user's GPC balance
        $stmt = $db->prepare("UPDATE users SET gpc_balance = COALESCE(gpc_balance, 0) + ? WHERE id = ?");
        $stmt->execute([$scoreValueData, $gpad_user['id']]);
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?saved=1');
    exit;
}

// Get user GPC balance
$db = db_get_connection();
$stmt = $db->prepare("SELECT COALESCE(gpc_balance, 0) as gpc_balance FROM users WHERE id = ?");
$stmt->execute([$gpad_user['id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$gpad_user_balance = (int)($row['gpc_balance'] ?? 0);
$gpad_user_name = htmlspecialchars($gpad_user['name']);
?><div id="gpad-user" style="position:fixed;top:0;left:0;right:0;z-index:9999;background:black;color:gold;padding:8px 16px;display:flex;justify-content:space-between;align-items:center;font-size:0.8rem;font-weight:700;font-family:Arial,sans-serif;">
    <span><i class="fas fa-user"></i> <?= $gpad_user_name ?></span>
    <span><i class="fa fa-gamepad"></i> <?= number_format($gpad_user_balance) ?> GPC</span>
</div>
