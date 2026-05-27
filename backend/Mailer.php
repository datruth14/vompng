<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function mailer_send_otp($email, $name, $otp)
{
    $host = getenv('SMTP_HOST') ?: $_ENV['SMTP_HOST'] ?? '';
    $port = getenv('SMTP_PORT') ?: $_ENV['SMTP_PORT'] ?? '587';
    $user = getenv('SMTP_USER') ?: $_ENV['SMTP_USER'] ?? '';
    $pass = getenv('SMTP_PASS') ?: $_ENV['SMTP_PASS'] ?? '';
    $from = getenv('SMTP_FROM') ?: $_ENV['SMTP_FROM'] ?? 'noreply@vomp.ng';
    $fromName = getenv('SMTP_FROM_NAME') ?: $_ENV['SMTP_FROM_NAME'] ?? 'vomp';

    if (empty($host) || empty($user) || empty($pass)) {
        return ['success' => false, 'error' => 'Mail server not configured.'];
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->SMTPSecure = (int) $port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = (int) $port;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom($from, $fromName);
        $mail->addAddress($email, $name);
        $mail->isHTML(true);

        $mail->Subject = 'Your OTP for Password Reset - vomp';
        $mail->Body = "
            <div style='font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 32px; background: #0a0a0a; color: #fff; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1);'>
                <div style='text-align: center; margin-bottom: 32px;'>
                    <div style='font-size: 28px; font-weight: 900; letter-spacing: 2px; color: #ff610a;'>vomp</div>
                </div>
                <p style='font-size: 16px; margin-bottom: 8px;'>Hi $name,</p>
                <p style='font-size: 14px; color: #aaa; margin-bottom: 24px;'>Use the OTP below to reset your password. It expires in 15 minutes.</p>
                <div style='text-align: center; margin: 32px 0;'>
                    <span style='display: inline-block; font-size: 36px; font-weight: 900; letter-spacing: 12px; color: #ff610a; background: rgba(255,97,10,0.1); padding: 16px 32px; border-radius: 16px;'>$otp</span>
                </div>
                <p style='font-size: 12px; color: #666; text-align: center;'>If you didn't request this, ignore this email.</p>
            </div>
        ";

        $mail->send();
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}
