<?php

require_once __DIR__ . '/../backend/Database.php';

$email = trim($_POST['email'] ?? '');
$otp = trim($_POST['otp'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($otp) || empty($password)) {
    header('Location: /reset-password?email=' . urlencode($email) . '&error=' . urlencode('All fields are required.'));
    exit;
}

if (strlen($otp) !== 6 || !ctype_digit($otp)) {
    header('Location: /reset-password?email=' . urlencode($email) . '&error=' . urlencode('Invalid OTP format.'));
    exit;
}

if (strlen($password) < 6) {
    header('Location: /reset-password?email=' . urlencode($email) . '&error=' . urlencode('Password must be at least 6 characters.'));
    exit;
}

$db = db_get_connection();

// Find valid OTP
$row = db_fetch(
    'SELECT id FROM password_resets WHERE email = ? AND otp = ? AND used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1',
    [$email, $otp]
);

if (!$row) {
    header('Location: /reset-password?email=' . urlencode($email) . '&error=' . urlencode('Invalid or expired OTP. Request a new one.'));
    exit;
}

// Update password
$hashed = password_hash($password, PASSWORD_BCRYPT);
$db->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?')->execute([$hashed, $email]);

// Mark OTP as used
$db->prepare('UPDATE password_resets SET used = 1 WHERE id = ?')->execute([$row['id']]);

header('Location: /login?success=' . urlencode('Password reset successful. Sign in with your new password.'));
exit;
