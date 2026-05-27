<?php

require_once __DIR__ . '/../backend/Database.php';

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

// Get user's store phone number
$store = db_fetch('SELECT contact_phone FROM stores WHERE owner_id = ? AND contact_phone IS NOT NULL AND contact_phone != "" LIMIT 1', [$user['id']]);
$phone = $store ? preg_replace('/[^0-9]/', '', $store['contact_phone']) : '';

if (empty($phone)) {
    header('Location: /forgot-password?error=' . urlencode('No phone number found on your store. Set a contact phone in store settings first.'));
    exit;
}

// Format phone for WhatsApp (remove leading 0 or +234)
if (str_starts_with($phone, '0')) {
    $phone = '234' . substr($phone, 1);
} elseif (str_starts_with($phone, '234')) {
    // already correct
} else {
    $phone = '234' . $phone;
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

// Build WhatsApp URL
$message = 'Your vomp password reset OTP is: ' . $otp . '. It expires in 15 minutes.';
$waUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($message);

header('Location: /reset-password?email=' . urlencode($email) . '&wa=' . urlencode($waUrl) . '&success=' . urlencode('OTP sent to your WhatsApp. Open it to see your code.'));
exit;
