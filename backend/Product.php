<?php
/*
 * Product helper functions.
 * Includes product creation, update, delete, and listing by store.
 */


require_once __DIR__ . '/Database.php';

/* Return all products for a given store ID. */

function product_get_products_by_store($storeId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('
        SELECT p.* FROM products p
        WHERE p.store_id = ? OR p.store_id = (SELECT s.owner_id FROM stores s WHERE s.id = ?)
        ORDER BY p.created_at DESC
    ');
    $stmt->execute([$storeId, $storeId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Return only available products for a store. */

function product_get_available_products_by_store($storeId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('
        SELECT * FROM products
        WHERE (store_id = ? OR store_id = (SELECT owner_id FROM stores WHERE id = ?))
        AND is_available = 1
        ORDER BY created_at DESC
    ');
    $stmt->execute([$storeId, $storeId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_get_available_products_by_store_paginated($storeId, $page = 1, $perPage = 12)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT * FROM products
        WHERE (store_id = ? OR store_id = (SELECT owner_id FROM stores WHERE id = ?))
        AND is_available = 1
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, $storeId);
    $stmt->bindValue(2, $storeId);
    $stmt->bindValue(3, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(4, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_count_available_by_store($storeId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('
        SELECT COUNT(*) FROM products
        WHERE (store_id = ? OR store_id = (SELECT owner_id FROM stores WHERE id = ?))
        AND is_available = 1
    ');
    $stmt->execute([$storeId, $storeId]);
    return (int) $stmt->fetchColumn();
}

/* Return all available products across all stores with store info. */

function product_get_all_available()
{
    $db = db_get_connection();
    $stmt = $db->query('
        SELECT p.*, s.name AS store_name, s.slug AS store_slug, s.contact_phone
        FROM products p
        LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        ORDER BY p.created_at DESC
    ');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_get_all_available_paginated($page = 1, $perPage = 50)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT p.*, s.name AS store_name, s.slug AS store_slug, s.contact_phone
        FROM products p
        LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_count_all_available()
{
    $db = db_get_connection();
    return (int) $db->query('SELECT COUNT(*) FROM products p LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id')->fetchColumn();
}

function product_get_by_category_paginated($category, $page = 1, $perPage = 50)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT p.*, s.name AS store_name, s.slug AS store_slug, s.contact_phone
        FROM products p
        LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        WHERE p.category = ?
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, $category);
    $stmt->bindValue(2, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(3, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_count_by_category($category)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT COUNT(*) FROM products p LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id WHERE p.category = ?');
    $stmt->execute([$category]);
    return (int) $stmt->fetchColumn();
}

function product_search_paginated($query, $page = 1, $perPage = 50)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT p.*, s.name AS store_name, s.slug AS store_slug, s.contact_phone
        FROM products p
        LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        WHERE p.name LIKE ? OR p.description LIKE ? OR s.name LIKE ?
        ORDER BY p.created_at DESC
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

function product_count_search($query)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $stmt = $db->prepare('SELECT COUNT(*) FROM products p LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id WHERE p.name LIKE ? OR p.description LIKE ? OR s.name LIKE ?');
    $stmt->execute([$like, $like, $like]);
    return (int) $stmt->fetchColumn();
}

/* Return products filtered by category. */

function product_get_by_category($category)
{
    $db = db_get_connection();
    $stmt = $db->prepare('
        SELECT p.*, s.name AS store_name, s.slug AS store_slug, s.contact_phone
        FROM products p
        LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        WHERE p.category = ?
        ORDER BY p.created_at DESC
    ');
    $stmt->execute([$category]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Search products by name, description, or store name. */

function product_search($query)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $stmt = $db->prepare('
        SELECT p.*, s.name AS store_name, s.slug AS store_slug, s.contact_phone
        FROM products p
        LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        WHERE p.name LIKE ? OR p.description LIKE ? OR s.name LIKE ?
        ORDER BY p.created_at DESC
    ');
    $stmt->execute([$like, $like, $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Return all distinct product categories. */

function product_get_categories()
{
    $db = db_get_connection();
    $stmt = $db->query('SELECT name FROM product_categories WHERE is_active = 1 ORDER BY sort_order ASC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return array_map(fn($r) => $r['name'], $rows);
}

/* Return a single product by ID. */

function product_get_by_id($productId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* Return a product only if it belongs to the given store and is available. */

function product_get_by_id_and_store($productId, $storeId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('
        SELECT * FROM products
        WHERE id = ? AND (store_id = ? OR store_id = (SELECT owner_id FROM stores WHERE id = ?))
    ');
    $stmt->execute([$productId, $storeId, $storeId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* Create a new product record for a store. */

function product_create($storeId, $name, $price, $description = '', $mediaUrl = '', $mediaType = 'image', $category = '')
{
    $db = db_get_connection();
    $id = bin2hex(random_bytes(12));

    $stmt = $db->prepare(
        'INSERT INTO products (id, name, price, description, media_url, media_type, category, store_id, is_available, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
    );

    $result = $stmt->execute([$id, $name, $price, $description, $mediaUrl, $mediaType, $category ?: 'Others', $storeId]);

    return $result ? ['success' => true, 'id' => $id, 'message' => 'Product created'] : ['success' => false, 'error' => 'Failed to create product'];
}

/* Update fields for an existing product. */

function product_update($productId, $data)
{
    $allowed = ['name', 'price', 'description', 'media_url', 'media_type', 'is_available', 'category'];
    $updateData = array_intersect_key($data, array_flip($allowed));

    if (empty($updateData)) {
        return ['success' => false, 'error' => 'No valid data to update'];
    }

    $updateData['updated_at'] = date('Y-m-d H:i:s');
    $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($updateData)));
    $sql = "UPDATE products SET $set WHERE id = ?";

    $db = db_get_connection();
    $stmt = $db->prepare($sql);
    $result = $stmt->execute(array_merge(array_values($updateData), [$productId]));

    return $result ? ['success' => true, 'message' => 'Product updated'] : ['success' => false, 'error' => 'Failed to update product'];
}

/* Delete a specific product by ID. */

function product_delete($productId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('DELETE FROM products WHERE id = ?');
    $result = $stmt->execute([$productId]);

    return $result ? ['success' => true, 'message' => 'Product deleted'] : ['success' => false, 'error' => 'Failed to delete product'];
}

/* Return all products across all stores owned by a user. */

function product_get_by_user_id($userId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT p.*, s.name AS store_name, s.slug AS store_slug FROM products p JOIN stores s ON p.store_id = s.id WHERE s.owner_id = ? ORDER BY p.created_at DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_get_by_user_id_paginated($userId, $page = 1, $perPage = 50)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('SELECT p.*, s.name AS store_name, s.slug AS store_slug FROM products p JOIN stores s ON p.store_id = s.id WHERE s.owner_id = ? ORDER BY p.created_at DESC LIMIT ? OFFSET ?');
    $stmt->bindValue(1, $userId);
    $stmt->bindValue(2, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(3, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_count_by_user_id($userId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT COUNT(*) FROM products p JOIN stores s ON p.store_id = s.id WHERE s.owner_id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}
