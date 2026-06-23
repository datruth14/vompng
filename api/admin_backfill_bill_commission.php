<?php
require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/BillPayment.php';

if (!auth_is_admin()) {
    http_response_code(403);
    echo 'Admin only';
    exit;
}

$db = db_get_connection();

// Ensure column exists first
$found = false;
$q = $db->query("SHOW COLUMNS FROM bill_payments");
foreach ($q as $c) {
    if ($c['Field'] === 'commission') { $found = true; break; }
}
if (!$found) {
    $db->exec("ALTER TABLE bill_payments ADD COLUMN commission DECIMAL(10,2) DEFAULT 0");
}

// Backfill: set commission = amount_naira * 5 / 100 for records where commission IS NULL or 0
$stmt = $db->prepare("UPDATE bill_payments SET commission = ROUND(amount_naira * ? / 100, 2) WHERE commission IS NULL OR commission = 0");
$stmt->execute([BILL_COMMISSION_PERCENT]);
$updated = $stmt->rowCount();

$total = (int) $db->query("SELECT COUNT(*) FROM bill_payments")->fetchColumn();
$withComm = (int) $db->query("SELECT COUNT(*) FROM bill_payments WHERE commission > 0")->fetchColumn();
$sumComm = (float) $db->query("SELECT COALESCE(SUM(commission), 0) FROM bill_payments")->fetchColumn();
?>
<!DOCTYPE html>
<html><head><title>Backfill Complete</title>
<style>body{background:#0f0f0f;color:#fff;font-family:system-ui;padding:2rem;max-width:600px;margin:auto;}
.card{background:rgba(255,255,255,.05);border-radius:1.5rem;padding:2rem;border:1px solid rgba(255,255,255,.1);}
h1{font-size:2rem;font-weight:900;}
.stat{font-size:1.5rem;font-weight:900;color:#22c55e;}
.label{color:#6b7280;font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;margin-top:1rem;}
.btn{display:inline-block;margin-top:1.5rem;padding:.75rem 1.5rem;background:#ff610a;color:#fff;border-radius:1rem;font-weight:700;text-decoration:none;}</style>
</head><body>
<div class="card">
<h1>✅ Backfill Complete</h1>
<p class="label">Records Updated</p>
<p class="stat"><?php echo $updated; ?></p>
<p class="label">Total Bill Payments</p>
<p class="stat"><?php echo $total; ?></p>
<p class="label">Records with Commission</p>
<p class="stat"><?php echo $withComm; ?></p>
<p class="label">Total Commission</p>
<p class="stat">₦<?php echo number_format($sumComm, 2); ?></p>
<a href="/admin/orders" class="btn">Go to Admin Orders →</a>
</div>
</body></html>
