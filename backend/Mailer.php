<?php

function mailer_send($to, $subject, $html)
{
    $apiKey = getenv('RESEND_API_KEY') ?: $_ENV['RESEND_API_KEY'] ?? '';
    $from = getenv('RESEND_FROM') ?: $_ENV['RESEND_FROM'] ?? 'vomp <noreply@vomp.ng>';

    if (empty($apiKey)) {
        return ['success' => false, 'error' => 'Resend API key not configured.'];
    }

    $payload = json_encode([
        'from' => $from,
        'to' => [$to],
        'subject' => $subject,
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

function mailer_send_otp($email, $name, $otp)
{
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

    return mailer_send($email, 'Your OTP for Password Reset - vomp', $html);
}

function mailer_notify_withdrawal($userName, $userEmail, $amount, $nairaAmount, $bankName, $accountNumber)
{
    $html = '
        <div style="font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 32px; background: #0a0a0a; color: #fff; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1);">
            <div style="text-align: center; margin-bottom: 32px;">
                <div style="font-size: 28px; font-weight: 900; letter-spacing: 2px; color: #ff610a;">vomp</div>
            </div>
            <p style="font-size: 16px; margin-bottom: 8px;">Withdrawal Request</p>
            <p style="font-size: 14px; color: #aaa; margin-bottom: 24px;">' . htmlspecialchars($userName) . ' (' . htmlspecialchars($userEmail) . ') is trying to make a withdrawal:</p>
            <div style="background: rgba(255,97,10,0.1); padding: 24px; border-radius: 16px; margin-bottom: 24px;">
                <p style="font-size: 14px; color: #fff; margin: 4px 0;"><strong>Amount:</strong> ' . number_format($amount) . ' Vomp Coins (₦' . number_format($nairaAmount) . ')</p>
                <p style="font-size: 14px; color: #fff; margin: 4px 0;"><strong>Bank:</strong> ' . htmlspecialchars($bankName) . '</p>
                <p style="font-size: 14px; color: #fff; margin: 4px 0;"><strong>Account:</strong> ' . htmlspecialchars($accountNumber) . '</p>
            </div>
            <p style="font-size: 12px; color: #ff610a; text-align: center; font-weight: bold;">Please fund the Paystack wallet to enable this withdrawal</p>
        </div>';

    return mailer_send('virtualopenmarket@gmail.com', 'Withdrawal Request — ' . $userName . ' — ' . number_format($amount) . ' Vomp Coins', $html);
}

function mailer_notify_bill_payment($userName, $userEmail, $type, $serviceId, $customerId, $amount, $error)
{
    $html = '
        <div style="font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 32px; background: #0a0a0a; color: #fff; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1);">
            <div style="text-align: center; margin-bottom: 32px;">
                <div style="font-size: 28px; font-weight: 900; letter-spacing: 2px; color: #ff610a;">vomp</div>
            </div>
            <p style="font-size: 16px; margin-bottom: 8px;">Bill Payment Failed</p>
            <p style="font-size: 14px; color: #aaa; margin-bottom: 24px;">' . htmlspecialchars($userName) . ' (' . htmlspecialchars($userEmail) . ') tried to make a bill payment but it failed:</p>
            <div style="background: rgba(255,97,10,0.1); padding: 24px; border-radius: 16px; margin-bottom: 24px;">
                <p style="font-size: 14px; color: #fff; margin: 4px 0;"><strong>Type:</strong> ' . htmlspecialchars(ucfirst($type)) . '</p>
                <p style="font-size: 14px; color: #fff; margin: 4px 0;"><strong>Service:</strong> ' . htmlspecialchars($serviceId) . '</p>
                <p style="font-size: 14px; color: #fff; margin: 4px 0;"><strong>Customer:</strong> ' . htmlspecialchars($customerId) . '</p>
                <p style="font-size: 14px; color: #fff; margin: 4px 0;"><strong>Amount:</strong> ₦' . number_format($amount) . '</p>
                <p style="font-size: 14px; color: #f87171; margin: 4px 0;"><strong>Error:</strong> ' . htmlspecialchars($error) . '</p>
            </div>
            <p style="font-size: 12px; color: #ff610a; text-align: center; font-weight: bold;">Please fund the VTU.NG wallet to enable bill payments</p>
        </div>';

    return mailer_send('virtualopenmarket@gmail.com', 'Bill Payment Failed — ' . $userName . ' — ' . ucfirst($type), $html);
}
