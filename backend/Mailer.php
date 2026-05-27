<?php

function mailer_send_otp($email, $name, $otp)
{
    $apiKey = getenv('RESEND_API_KEY') ?: $_ENV['RESEND_API_KEY'] ?? '';
    $from = getenv('RESEND_FROM') ?: $_ENV['RESEND_FROM'] ?? 'vomp <noreply@vomp.ng>';

    if (empty($apiKey)) {
        return ['success' => false, 'error' => 'Resend API key not configured.'];
    }

    $html = '
        <div style="font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 32px; background: #0a0a0a; color: #fff; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1);">
            <div style="text-align: center; margin-bottom: 32px;">
                <div style="font-size: 28px; font-weight: 900; letter-spacing: 2px; color: #ff610a;">vomp</div>
            </div>
            <p style="font-size: 16px; margin-bottom: 8px;">Hi ' . htmlspecialchars($name) . ',</p>
            <p style="font-size: 14px; color: #aaa; margin-bottom: 24px;">Use the OTP below to reset your password. It expires in 15 minutes.</p>
            <div style="text-align: center; margin: 32px 0;">
                <span style="display: inline-block; font-size: 36px; font-weight: 900; letter-spacing: 12px; color: #ff610a; background: rgba(255,97,10,0.1); padding: 16px 32px; border-radius: 16px;">' . htmlspecialchars($otp) . '</span>
            </div>
            <p style="font-size: 12px; color: #666; text-align: center;">If you didn\'t request this, ignore this email.</p>
        </div>';

    $payload = json_encode([
        'from' => $from,
        'to' => [$email],
        'subject' => 'Your OTP for Password Reset - vomp',
        'html' => $html,
    ]);

    $options = [
        'http' => [
            'header' => "Authorization: Bearer $apiKey\r\nContent-Type: application/json\r\n",
            'method' => 'POST',
            'content' => $payload,
            'ignore_errors' => true,
        ],
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents('https://api.resend.com/emails', false, $context);

    if ($result === false) {
        return ['success' => false, 'error' => 'Failed to send email via Resend.'];
    }

    $response = json_decode($result, true);
    if (isset($response['id'])) {
        return ['success' => true];
    }

    $error = $response['message'] ?? $response['error'] ?? 'Unknown error';
    return ['success' => false, 'error' => $error];
}
