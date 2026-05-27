<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Mailer.php';

$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /forgot-password?error=' . urlencode('Invalid email address.'));
    exit;
}

$db = db_get_connection();

$user = db_fetch('SELECT id, name, email FROM users WHERE email = ?', [$email]);
if (!$user) {
    header('Location: /forgot-password?error=' . urlencode('No account found with that email.'));
    exit;
}

// Generate 6-digit OTP
$otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
$id = bin2hex(random_bytes(12));

// Invalidate previous unused OTPs for this email
$db->prepare('UPDATE password_resets SET used = 1 WHERE email = ? AND used = 0')->execute([$email]);

// Store new OTP
db_insert('password_resets', [
    'id' => $id,
    'email' => $email,
    'otp' => $otp,
    'expires_at' => $expiresAt,
    'used' => 0,
]);

// Send via Resend
$result = mailer_send_otp($email, $user['name'], $otp);

if (!$result['success']) {
    header('Location: /forgot-password?error=' . urlencode('Failed to send email. ' . ($result['error'] ?? 'Check Resend config.')));
    exit;
}

header('Location: /reset-password?email=' . urlencode($email) . '&success=' . urlencode('OTP sent to your email. It expires in 15 minutes.'));
exit;
