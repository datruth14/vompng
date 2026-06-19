<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Token.php';
require_once __DIR__ . '/../backend/BillPayment.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$type = $data['type'] ?? '';
$request_id = 'vomp_' . uniqid() . '_' . bin2hex(random_bytes(4));

$userId = $currentUser['id'];

switch ($type) {
    case 'airtime':
        $service_id = $data['service_id'] ?? '';
        $phone = $data['phone'] ?? '';
        $amount = (int) ($data['amount'] ?? 0);

        if (!$service_id || !$phone || $amount < 10) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid fields']);
            exit;
        }

        $coins = bill_naira_to_coins($amount);
        $balance = token_user_balance($userId);
        if ($balance < $coins) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient Vomp Coins', 'needed' => $coins, 'balance' => $balance]);
            exit;
        }

        $result = vtung_purchase_airtime($phone, $service_id, $amount, $request_id);
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $providerRef = $result['data']['order_id'] ?? '';
        $vtungStatus = str_starts_with($result['message'], 'ORDER COMPLETED') ? 'completed' : 'processing';
        $meta = json_encode($result['data']);
        $deduct = bill_deduct_coins($userId, $coins, $type, $service_id, $phone, $amount, $providerRef, $meta, $vtungStatus);

        echo json_encode([
            'success' => true,
            'message' => 'Airtime purchase successful',
            'data' => $result['data'],
            'coins_deducted' => $coins,
            'new_balance' => $deduct['new_balance'] ?? $balance - $coins,
        ]);
        break;

    case 'data':
        $service_id = $data['service_id'] ?? '';
        $phone = $data['phone'] ?? '';
        $variation_id = $data['variation_id'] ?? '';
        $amount = (int) ($data['amount'] ?? 0);

        if (!$service_id || !$phone || !$variation_id || !$amount) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid fields']);
            exit;
        }

        $coins = bill_naira_to_coins($amount);
        $balance = token_user_balance($userId);
        if ($balance < $coins) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient Vomp Coins', 'needed' => $coins, 'balance' => $balance]);
            exit;
        }

        $result = vtung_purchase_data($phone, $service_id, $variation_id, $request_id);
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $providerRef = $result['data']['order_id'] ?? '';
        $vtungStatus = str_starts_with($result['message'], 'ORDER COMPLETED') ? 'completed' : 'processing';
        $meta = json_encode($result['data']);
        $deduct = bill_deduct_coins($userId, $coins, $type, $service_id, $phone, $amount, $providerRef, $meta, $vtungStatus);

        echo json_encode([
            'success' => true,
            'message' => 'Data purchase successful',
            'data' => $result['data'],
            'coins_deducted' => $coins,
            'new_balance' => $deduct['new_balance'] ?? $balance - $coins,
        ]);
        break;

    case 'electricity':
        $service_id = $data['service_id'] ?? '';
        $customer_id = $data['customer_id'] ?? '';
        $variation_id = $data['variation_id'] ?? 'prepaid';
        $amount = (int) ($data['amount'] ?? 0);

        if (!$service_id || !$customer_id || !$amount) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid fields']);
            exit;
        }

        $coins = bill_naira_to_coins($amount);
        $balance = token_user_balance($userId);
        if ($balance < $coins) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient Vomp Coins', 'needed' => $coins, 'balance' => $balance]);
            exit;
        }

        $result = vtung_purchase_electricity($customer_id, $service_id, $variation_id, $amount, $request_id);
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $providerRef = $result['data']['order_id'] ?? '';
        $vtungStatus = str_starts_with($result['message'], 'ORDER COMPLETED') ? 'completed' : 'processing';
        $meta = json_encode($result['data']);
        $deduct = bill_deduct_coins($userId, $coins, $type, $service_id, $customer_id, $amount, $providerRef, $meta, $vtungStatus);

        echo json_encode([
            'success' => true,
            'message' => 'Electricity payment successful',
            'data' => $result['data'],
            'coins_deducted' => $coins,
            'new_balance' => $deduct['new_balance'] ?? $balance - $coins,
        ]);
        break;

    case 'tv':
        $service_id = $data['service_id'] ?? '';
        $customer_id = $data['customer_id'] ?? '';
        $variation_id = $data['variation_id'] ?? '';
        $amount = (int) ($data['amount'] ?? 0);
        $subscription_type = $data['subscription_type'] ?? 'change';

        if (!$service_id || !$customer_id || !$variation_id || !$amount) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid fields']);
            exit;
        }

        $coins = bill_naira_to_coins($amount);
        $balance = token_user_balance($userId);
        if ($balance < $coins) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient Vomp Coins', 'needed' => $coins, 'balance' => $balance]);
            exit;
        }

        $result = vtung_purchase_tv($customer_id, $service_id, $variation_id, $request_id, $subscription_type, $amount);
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $providerRef = $result['data']['order_id'] ?? '';
        $vtungStatus = str_starts_with($result['message'], 'ORDER COMPLETED') ? 'completed' : 'processing';
        $meta = json_encode($result['data']);
        $deduct = bill_deduct_coins($userId, $coins, $type, $service_id, $customer_id, $amount, $providerRef, $meta, $vtungStatus);

        echo json_encode([
            'success' => true,
            'message' => 'Cable TV subscription successful',
            'data' => $result['data'],
            'coins_deducted' => $coins,
            'new_balance' => $deduct['new_balance'] ?? $balance - $coins,
        ]);
        break;

    case 'betting':
        $service_id = $data['service_id'] ?? '';
        $customer_id = $data['customer_id'] ?? '';
        $amount = (int) ($data['amount'] ?? 0);

        if (!$service_id || !$customer_id || !$amount) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid fields']);
            exit;
        }

        $coins = bill_naira_to_coins($amount);
        $balance = token_user_balance($userId);
        if ($balance < $coins) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient Vomp Coins', 'needed' => $coins, 'balance' => $balance]);
            exit;
        }

        $result = vtung_purchase_betting($customer_id, $service_id, $amount, $request_id);
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $providerRef = $result['data']['order_id'] ?? '';
        $vtungStatus = str_starts_with($result['message'], 'ORDER COMPLETED') ? 'completed' : 'processing';
        $meta = json_encode($result['data']);
        $deduct = bill_deduct_coins($userId, $coins, $type, $service_id, $customer_id, $amount, $providerRef, $meta, $vtungStatus);

        echo json_encode([
            'success' => true,
            'message' => 'Betting account funded successfully',
            'data' => $result['data'],
            'coins_deducted' => $coins,
            'new_balance' => $deduct['new_balance'] ?? $balance - $coins,
        ]);
        break;

    case 'epins':
        $service_id = $data['service_id'] ?? '';
        $value = (int) ($data['value'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? 1);

        if (!$service_id || !$value || $quantity < 1) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid fields']);
            exit;
        }

        $amount = $value * $quantity;
        $coins = bill_naira_to_coins($amount);
        $balance = token_user_balance($userId);
        if ($balance < $coins) {
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient Vomp Coins', 'needed' => $coins, 'balance' => $balance]);
            exit;
        }

        $result = vtung_purchase_epins($service_id, $value, $quantity, $request_id);
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $providerRef = $result['data']['order_id'] ?? '';
        $vtungStatus = str_starts_with($result['message'], 'ORDER COMPLETED') ? 'completed' : 'processing';
        $meta = json_encode($result['data']);
        $customerId = 'qty_' . $quantity . '_val_' . $value;
        $deduct = bill_deduct_coins($userId, $coins, $type, $service_id, $customerId, $amount, $providerRef, $meta, $vtungStatus);

        echo json_encode([
            'success' => true,
            'message' => 'ePINs purchased successfully',
            'data' => $result['data'],
            'coins_deducted' => $coins,
            'new_balance' => $deduct['new_balance'] ?? $balance - $coins,
        ]);
        break;

    case 'verify':
        $customer_id = $data['customer_id'] ?? '';
        $service_id = $data['service_id'] ?? '';
        $variation_id = $data['variation_id'] ?? null;

        if (!$customer_id || !$service_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing customer_id or service_id']);
            exit;
        }

        $result = vtung_verify_customer($customer_id, $service_id, $variation_id);
        if (!$result['success']) {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $result['data'],
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid bill payment type']);
}
