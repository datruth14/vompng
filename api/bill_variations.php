<?php

require_once __DIR__ . '/../backend/BillPayment.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$service_id = $_GET['service_id'] ?? null;

switch ($type) {
    case 'data':
        $variations = vtung_get_data_variations($service_id);
        echo json_encode(['success' => true, 'data' => $variations]);
        break;
    case 'tv':
        $variations = vtung_get_tv_variations($service_id);
        echo json_encode(['success' => true, 'data' => $variations]);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid type. Use ?type=data or ?type=tv']);
}
