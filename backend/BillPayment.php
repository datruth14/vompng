<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Token.php';

/* VTU.NG API helpers */

function vtung_get_token()
{
    $cacheFile = __DIR__ . '/../cache/vtung_token.json';
    $now = time();

    if (is_file($cacheFile)) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached && isset($cached['token']) && $cached['expires_at'] > $now) {
            return $cached['token'];
        }
    }

    $username = getenv('VTU_NG_USERNAME') ?: '';
    $password = getenv('VTU_NG_PASSWORD') ?: '';
    if (!$username || !$password) {
        return null;
    }

    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode(['username' => $username, 'password' => $password]),
            'timeout' => 30,
            'ignore_errors' => true,
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents('https://vtu.ng/wp-json/jwt-auth/v1/token', false, $context);
    $body = json_decode($response, true);

    if (!$body || !isset($body['token'])) {
        return null;
    }

    $cache = [
        'token' => $body['token'],
        'expires_at' => $now + (6 * 86400),
    ];
    @file_put_contents($cacheFile, json_encode($cache));

    return $body['token'];
}

function vtung_http_post($endpoint, $data)
{
    $token = vtung_get_token();
    if (!$token) {
        return ['success' => false, 'error' => 'VTU.NG authentication failed'];
    }

    $url = 'https://vtu.ng/wp-json/api/v2/' . ltrim($endpoint, '/');
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            'content' => json_encode($data),
            'timeout' => 30,
            'ignore_errors' => true,
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
    $httpCode = 0;
    if (isset($http_response_header) && preg_match('/HTTP\/\d+\.?\d*\s+(\d+)/', $http_response_header[0], $m)) {
        $httpCode = (int) $m[1];
    }
    $body = json_decode($response, true);

    if (!$body || !isset($body['code']) || $body['code'] !== 'success') {
        $msg = $body['message'] ?? 'VTU.NG request failed';
        return ['success' => false, 'error' => $msg, 'http_code' => $httpCode, 'response' => $body];
    }

    return ['success' => true, 'data' => $body['data'] ?? [], 'message' => $body['message'] ?? ''];
}

function vtung_purchase_airtime($phone, $service_id, $amount, $request_id)
{
    return vtung_http_post('airtime', [
        'request_id' => $request_id,
        'phone' => preg_replace('/[^0-9]/', '', $phone),
        'service_id' => $service_id,
        'amount' => (int) $amount,
    ]);
}

function vtung_purchase_data($phone, $service_id, $variation_id, $request_id)
{
    return vtung_http_post('data', [
        'request_id' => $request_id,
        'phone' => preg_replace('/[^0-9]/', '', $phone),
        'service_id' => $service_id,
        'variation_id' => (string) $variation_id,
    ]);
}

function vtung_purchase_electricity($customer_id, $service_id, $variation_id, $amount, $request_id)
{
    return vtung_http_post('electricity', [
        'request_id' => $request_id,
        'customer_id' => $customer_id,
        'service_id' => $service_id,
        'variation_id' => $variation_id,
        'amount' => (int) $amount,
    ]);
}

function vtung_purchase_tv($customer_id, $service_id, $variation_id, $request_id, $subscription_type = 'change', $amount = null)
{
    $data = [
        'request_id' => $request_id,
        'customer_id' => $customer_id,
        'service_id' => $service_id,
        'variation_id' => (string) $variation_id,
        'subscription_type' => $subscription_type,
    ];
    if ($amount !== null) {
        $data['amount'] = (int) $amount;
    }
    return vtung_http_post('tv', $data);
}

function vtung_get_data_variations($service_id = null)
{
    $url = 'https://vtu.ng/wp-json/api/v2/variations/data';
    if ($service_id) {
        $url .= '?service_id=' . urlencode($service_id);
    }
    $opts = [
        'http' => ['method' => 'GET', 'timeout' => 10, 'ignore_errors' => true],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
    $body = json_decode($response, true);
    if (!$body || !isset($body['code']) || $body['code'] !== 'success') {
        return [];
    }
    return $body['data'] ?? [];
}

function vtung_get_tv_variations($service_id = null)
{
    $url = 'https://vtu.ng/wp-json/api/v2/variations/tv';
    if ($service_id) {
        $url .= '?service_id=' . urlencode($service_id);
    }
    $opts = [
        'http' => ['method' => 'GET', 'timeout' => 10, 'ignore_errors' => true],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);
    $body = json_decode($response, true);
    if (!$body || !isset($body['code']) || $body['code'] !== 'success') {
        return [];
    }
    return $body['data'] ?? [];
}

/* Verify customer with VTU.NG */

function vtung_verify_customer($customer_id, $service_id, $variation_id = null)
{
    $data = [
        'customer_id' => $customer_id,
        'service_id' => $service_id,
    ];
    if ($variation_id) {
        $data['variation_id'] = $variation_id;
    }
    return vtung_http_post('verify-customer', $data);
}

/* Deduct Vomp Coins for bill payment, log transaction, and save bill_payments record. */

function bill_deduct_coins($userId, $coinsToDeduct, $type, $serviceId, $customerId, $amountNaira, $providerRef = '', $metaData = '', $vtungStatus = 'processing')
{
    $db = db_get_connection();

    $stmt = $db->prepare('SELECT token_balance FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user || (int) $user['token_balance'] < $coinsToDeduct) {
        return ['success' => false, 'error' => 'Insufficient Vomp Coins'];
    }

    $newBalance = (int) $user['token_balance'] - $coinsToDeduct;

    $db->beginTransaction();
    try {
        $stmt = $db->prepare('UPDATE users SET token_balance = ? WHERE id = ?');
        $stmt->execute([$newBalance, $userId]);

        $stmt = $db->prepare('INSERT INTO token_transactions (id, user_id, type, amount, description, created_at) VALUES (?, ?, \'debit\', ?, ?, NOW())');
        $stmt->execute([
            bin2hex(random_bytes(12)),
            $userId,
            $coinsToDeduct,
            "Bill payment: {$type} - ₦" . number_format($amountNaira) . " ({$serviceId})",
        ]);

        $stmt = $db->prepare('INSERT INTO bill_payments (id, user_id, type, service_id, customer_id, amount_naira, coins_deducted, provider_ref, status, meta_data, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            bin2hex(random_bytes(12)),
            $userId,
            $type,
            $serviceId,
            $customerId,
            $amountNaira,
            $coinsToDeduct,
            $providerRef,
            $vtungStatus,
            $metaData,
        ]);

        $db->commit();
        return ['success' => true, 'new_balance' => $newBalance, 'deducted' => $coinsToDeduct];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'error' => 'Failed to process payment'];
    }
}

/* Calculate Vomp Coins needed for a naira amount. */

function bill_naira_to_coins($amountNaira)
{
    return (int) ceil($amountNaira / TOKEN_PRICE_PER_UNIT);
}
