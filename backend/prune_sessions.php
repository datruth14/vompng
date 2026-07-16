<?php
/*
 * CLI script to remove expired sessions.
 * Deletes old session records and logs the pruning result.
 */


// CLI script to remove expired sessions from the database.
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Logger.php';

$db = db_get_connection();

try {
    $stmt = $db->prepare("DELETE FROM sessions WHERE expires_at <= NOW()");
    $stmt->execute();
    $count = $stmt->rowCount();
    logger_info("Pruned {$count} expired sessions");
    echo "Pruned {$count} expired sessions\n";
    exit(0);
} catch (Exception $e) {
    logger_info('Session prune failed: ' . $e->getMessage());
    fwrite(STDERR, "Error pruning sessions: " . $e->getMessage() . "\n");
    exit(2);
}
