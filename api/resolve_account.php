<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Paystack.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$accountNumber = trim($data['account_number'] ?? '');
$bankCode = trim($data['bank_code'] ?? '');

if (!preg_match('/^\d{10}$/', $accountNumber)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Account number must be 10 digits']);
    exit;
}

$results = [];

if ($bankCode) {
    // Resolve for a specific bank only
    $resolved = paystack_resolve_account($accountNumber, $bankCode);
    if ($resolved !== null) {
        $banks = paystack_list_banks();
        $bankName = '';
        foreach ($banks as $b) {
            if ($b['code'] === $bankCode) { $bankName = $b['name']; break; }
        }
        $results[] = [
            'bank_code' => $bankCode,
            'bank_name' => $bankName,
            'account_number' => $resolved['account_number'],
            'account_name' => $resolved['account_name'],
        ];
    }
} else {
    // Legacy: resolve against all banks
    $banks = paystack_list_banks();
    foreach ($banks as $bank) {
        $code = $bank['code'] ?? '';
        $name = $bank['name'] ?? '';
        if (!$code || !$name) continue;

        $resolved = paystack_resolve_account($accountNumber, $code);
        if ($resolved !== null) {
            $results[] = [
                'bank_code' => $code,
                'bank_name' => $name,
                'account_number' => $resolved['account_number'],
                'account_name' => $resolved['account_name'],
            ];
        }
    }
}

echo json_encode(['success' => true, 'results' => $results]);
