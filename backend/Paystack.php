<?php

function paystack_load_config()
{
    static $loaded = false;
    if ($loaded) return;

    $envFile = __DIR__ . '/../.env';
    if (is_file($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1], " \t\"'");
                putenv("$key=$val");
                $_ENV[$key] = $val;
            }
        }
    }
    $loaded = true;
}

function paystack_secret_key()
{
    paystack_load_config();
    return getenv('PAYSTACK_SECRET_KEY') ?: '';
}

function paystack_public_key()
{
    paystack_load_config();
    return getenv('PAYSTACK_PUBLIC_KEY') ?: '';
}

function paystack_http_post($url, $data)
{
    $sk = paystack_secret_key();
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => [
                'Authorization: Bearer ' . $sk,
                'Content-Type: application/json',
            ],
            'content' => json_encode($data),
            'timeout' => 30,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
    $httpCode = 0;
    if (isset($http_response_header) && preg_match('/HTTP\/\d+\.?\d*\s+(\d+)/', $http_response_header[0], $m)) {
        $httpCode = (int) $m[1];
    }
    return [$response, $httpCode];
}

function paystack_http_get($url)
{
    $sk = paystack_secret_key();
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'Authorization: Bearer ' . $sk,
                'Content-Type: application/json',
            ],
            'timeout' => 30,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
    $httpCode = 0;
    if (isset($http_response_header) && preg_match('/HTTP\/\d+\.?\d*\s+(\d+)/', $http_response_header[0], $m)) {
        $httpCode = (int) $m[1];
    }
    return [$response, $httpCode];
}

function paystack_initialize($email, $amountKobo, $metadata = [], $callbackUrl = '')
{
    $sk = paystack_secret_key();
    if (!$sk) {
        return ['success' => false, 'error' => 'Paystack secret key not configured'];
    }

    $params = [
        'email' => $email,
        'amount' => (int) $amountKobo,
        'metadata' => $metadata,
    ];

    if ($callbackUrl) {
        $params['callback_url'] = $callbackUrl;
    }

    [$response, $httpCode] = paystack_http_post('https://api.paystack.co/transaction/initialize', $params);

    $body = json_decode($response, true);

    if ($httpCode !== 200 || !$body || !($body['status'] ?? false)) {
        $msg = $body['message'] ?? 'Paystack initialization failed';
        return ['success' => false, 'error' => $msg];
    }

    return [
        'success' => true,
        'authorization_url' => $body['data']['authorization_url'],
        'reference' => $body['data']['reference'],
    ];
}

function paystack_verify($reference)
{
    $sk = paystack_secret_key();
    if (!$sk) {
        return ['success' => false, 'error' => 'Paystack secret key not configured'];
    }

    [$response, $httpCode] = paystack_http_get("https://api.paystack.co/transaction/verify/" . urlencode($reference));

    $body = json_decode($response, true);

    if ($httpCode !== 200 || !$body || !($body['status'] ?? false)) {
        return ['success' => false, 'error' => 'Payment verification failed'];
    }

    $data = $body['data'];
    if ($data['status'] !== 'success') {
        return ['success' => false, 'error' => 'Payment was not successful'];
    }

    return [
        'success' => true,
        'amount' => $data['amount'],
        'reference' => $data['reference'],
        'metadata' => $data['metadata'] ?? [],
    ];
}

/* Fetch all Nigerian banks from Paystack. */

function paystack_list_banks()
{
    $cacheFile = __DIR__ . '/../cache/banks.json';
    if (is_file($cacheFile) && time() - filemtime($cacheFile) < 86400) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached) return $cached;
    }

    $all = [];
    $page = 1;
    $perPage = 100;
    while (true) {
        [$response, $httpCode] = paystack_http_get("https://api.paystack.co/bank?country=nigeria&perPage={$perPage}&page={$page}");
        $body = json_decode($response, true);
        if ($httpCode !== 200 || !$body || !($body['status'] ?? false)) break;
        $data = $body['data'] ?? [];
        if (empty($data)) break;
        $all = array_merge($all, $data);
        if (count($data) < $perPage) break;
        $page++;
    }

    if (!empty($all)) {
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents($cacheFile, json_encode($all));
    }

    return $all;
}

/* Resolve account name for a given account number and bank code. */

function paystack_resolve_account($accountNumber, $bankCode)
{
    [$response, $httpCode] = paystack_http_get("https://api.paystack.co/bank/resolve?account_number={$accountNumber}&bank_code={$bankCode}");
    $body = json_decode($response, true);
    if ($httpCode !== 200 || !$body || !($body['status'] ?? false)) {
        return null;
    }
    return [
        'account_number' => $body['data']['account_number'] ?? $accountNumber,
        'account_name' => $body['data']['account_name'] ?? '',
    ];
}

/* Create a transfer recipient on Paystack. */

function paystack_create_transfer_recipient($bankCode, $accountNumber, $accountName, &$errorMsg = '')
{
    $sk = paystack_secret_key();
    $fields = [
        'type' => 'nuban',
        'name' => $accountName,
        'account_number' => $accountNumber,
        'bank_code' => $bankCode,
        'currency' => 'NGN',
    ];

    [$response, $httpCode] = paystack_http_post('https://api.paystack.co/transferrecipient', $fields);
    $body = json_decode($response, true);

    if ($httpCode !== 201 || !$body || !($body['status'] ?? false)) {
        $errorMsg = $body['message'] ?? 'Unknown error';
        return null;
    }

    return [
        'recipient_code' => $body['data']['recipient_code'] ?? '',
        'recipient_id' => $body['data']['id'] ?? '',
    ];
}

/* Initiate a transfer to a recipient. */

function paystack_initiate_transfer($recipientCode, $amountKobo, $reason = '')
{
    $sk = paystack_secret_key();
    $reference = 'wd_' . uniqid();

    $fields = [
        'source' => 'balance',
        'amount' => (int) $amountKobo,
        'recipient' => $recipientCode,
        'reason' => $reason ?: 'Vomp Coin withdrawal',
        'reference' => $reference,
    ];

    [$response, $httpCode] = paystack_http_post('https://api.paystack.co/transfer', $fields);
    $body = json_decode($response, true);

    if ($httpCode !== 200 || !$body || !($body['status'] ?? false)) {
        return ['success' => false, 'error' => $body['message'] ?? 'Transfer initiation failed'];
    }

    return [
        'success' => true,
        'transfer_code' => $body['data']['transfer_code'] ?? '',
        'status' => $body['data']['status'] ?? '',
        'amount' => $body['data']['amount'] ?? $amountKobo,
        'reference' => $reference,
    ];
}
