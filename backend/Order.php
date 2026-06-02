<?php

require_once __DIR__ . '/Database.php';

function order_create($storeId, $productId, $productName, $customerName, $customerEmail, $state, $deliveryLocation)
{
    try {
        $db = db_get_connection();
        $id = bin2hex(random_bytes(12));
        $stmt = $db->prepare('INSERT INTO orders (id, store_id, product_id, product_name, customer_name, customer_email, state, delivery_location, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$id, $storeId, $productId, $productName, $customerName, $customerEmail, $state, $deliveryLocation]);
        return ['success' => true, 'orderId' => $id];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function order_get_by_store($storeId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM orders WHERE store_id = ? ORDER BY created_at DESC');
    $stmt->execute([$storeId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function order_get_by_store_paginated($storeId, $page = 1, $perPage = 20)
{
    $db = db_get_connection();
    $offset = ($page - 1) * $perPage;

    $countStmt = $db->prepare('SELECT COUNT(*) FROM orders WHERE store_id = ?');
    $countStmt->execute([$storeId]);
    $total = (int) $countStmt->fetchColumn();

    $stmt = $db->prepare('SELECT * FROM orders WHERE store_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $stmt->bindValue(1, $storeId, PDO::PARAM_STR);
    $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'orders' => $orders,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => max(1, (int) ceil($total / $perPage))
    ];
}

function order_count_by_store($storeId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT COUNT(*) FROM orders WHERE store_id = ?');
    $stmt->execute([$storeId]);
    return (int) $stmt->fetchColumn();
}
