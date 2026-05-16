<?php

// CLI script to remove expired sessions from the database.
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Logger.php';

use Backend\Database;
use Backend\Logger;

$db = Database::getInstance()->getConnection();

try {
    $stmt = $db->prepare("DELETE FROM sessions WHERE expires_at <= datetime('now')");
    $stmt->execute();
    $count = $stmt->rowCount();
    Logger::info("Pruned {$count} expired sessions");
    echo "Pruned {$count} expired sessions\n";
    exit(0);
} catch (\Exception $e) {
    Logger::info('Session prune failed: ' . $e->getMessage());
    fwrite(STDERR, "Error pruning sessions: " . $e->getMessage() . "\n");
    exit(2);
}
