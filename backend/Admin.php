<?php

require_once __DIR__ . '/Database.php';

function admin_count_users()
{
    return (int) db_get_connection()->query('SELECT COUNT(*) FROM users')->fetchColumn();
}

function admin_count_stores()
{
    return (int) db_get_connection()->query('SELECT COUNT(*) FROM stores')->fetchColumn();
}

function admin_count_products()
{
    return (int) db_get_connection()->query('SELECT COUNT(*) FROM products')->fetchColumn();
}

function admin_count_transactions()
{
    return (int) db_get_connection()->query('SELECT COUNT(*) FROM token_transactions')->fetchColumn();
}

function admin_get_users_paginated($page = 1, $perPage = 20)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT u.*, (SELECT COUNT(*) FROM stores WHERE owner_id = u.id) AS store_count
        FROM users u
        ORDER BY u.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function admin_count_users_total()
{
    return (int) db_get_connection()->query('SELECT COUNT(*) FROM users')->fetchColumn();
}

function admin_search_users_paginated($query, $page = 1, $perPage = 20)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT u.*, (SELECT COUNT(*) FROM stores WHERE owner_id = u.id) AS store_count
        FROM users u
        WHERE u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?
        ORDER BY u.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, $like);
    $stmt->bindValue(2, $like);
    $stmt->bindValue(3, $like);
    $stmt->bindValue(4, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(5, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function admin_count_search_users($query)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?');
    $stmt->execute([$like, $like, $like]);
    return (int) $stmt->fetchColumn();
}

function admin_get_stores_paginated($page = 1, $perPage = 20)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT s.*, u.name AS owner_name, u.email AS owner_email,
               (SELECT COUNT(*) FROM products p WHERE p.store_id = s.id OR p.store_id = s.owner_id) AS product_count
        FROM stores s
        LEFT JOIN users u ON s.owner_id = u.id
        ORDER BY product_count DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function admin_count_stores_total()
{
    return (int) db_get_connection()->query('SELECT COUNT(*) FROM stores')->fetchColumn();
}

function admin_search_stores_paginated($query, $page = 1, $perPage = 20)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT s.*, u.name AS owner_name, u.email AS owner_email,
               (SELECT COUNT(*) FROM products p WHERE p.store_id = s.id OR p.store_id = s.owner_id) AS product_count
        FROM stores s
        LEFT JOIN users u ON s.owner_id = u.id
        WHERE s.name LIKE ? OR s.slug LIKE ? OR u.name LIKE ? OR u.email LIKE ?
        ORDER BY product_count DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, $like);
    $stmt->bindValue(2, $like);
    $stmt->bindValue(3, $like);
    $stmt->bindValue(4, $like);
    $stmt->bindValue(5, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(6, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function admin_count_search_stores($query)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $stmt = $db->prepare('
        SELECT COUNT(*) FROM stores s
        LEFT JOIN users u ON s.owner_id = u.id
        WHERE s.name LIKE ? OR s.slug LIKE ? OR u.name LIKE ? OR u.email LIKE ?
    ');
    $stmt->execute([$like, $like, $like, $like]);
    return (int) $stmt->fetchColumn();
}

function admin_get_products_paginated($page = 1, $perPage = 20)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT p.*, s.name AS store_name, s.slug
        FROM products p
        JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function admin_count_products_total()
{
    return (int) db_get_connection()->query('SELECT COUNT(*) FROM products')->fetchColumn();
}

function admin_get_transactions_paginated($page = 1, $perPage = 30)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT t.*, s.name AS store_name, s.slug AS store_slug
        FROM token_transactions t
        JOIN stores s ON t.store_id = s.id
        ORDER BY t.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function admin_count_transactions_total()
{
    return (int) db_get_connection()->query('SELECT COUNT(*) FROM token_transactions')->fetchColumn();
}
