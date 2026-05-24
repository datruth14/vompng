<?php
/*
 * Store helper functions.
 * Includes store lookup, update operations, and store-product aggregation logic.
 */


require_once __DIR__ . '/Database.php';

/* Load an active store by its slug. */

function store_get_by_slug($slug)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM stores WHERE slug = ? AND is_active = 1');
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* Load a store owned by a specific user. */

function store_get_by_slug_for_owner($slug, $ownerId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM stores WHERE slug = ? AND owner_id = ? AND is_active = 1');
    $stmt->execute([$slug, $ownerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* Return all stores owned by a user. */

function store_get_user_stores($userId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM stores WHERE owner_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Update allowed store fields for an existing store. */

function store_update($storeId, $data)
{
    $allowed = [
        'name',
        'description',
        'contact_phone',
        'contact_email',
        'logo_url',
        'hero_image_url',
        'hero_color',
        'accent_color',
        'is_active',
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'social_tiktok',
        'social_youtube',
    ];
    $updateData = array_intersect_key($data, array_flip($allowed));

    if (empty($updateData)) {
        return ['success' => false, 'error' => 'No valid data to update'];
    }

    $updateData['updated_at'] = date('Y-m-d H:i:s');
    $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($updateData)));
    $sql = "UPDATE stores SET $set WHERE id = ?";

    $db = db_get_connection();
    $stmt = $db->prepare($sql);
    $result = $stmt->execute(array_merge(array_values($updateData), [$storeId]));

    return $result ? ['success' => true, 'message' => 'Store updated'] : ['success' => false, 'error' => 'Failed to update store'];
}

/* Return all active stores. */

function store_get_all_active()
{
    $db = db_get_connection();
    $stmt = $db->query('SELECT * FROM stores WHERE is_active = 1 ORDER BY created_at DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function store_get_all_active_paginated($page = 1, $perPage = 50)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('SELECT * FROM stores WHERE is_active = 1 ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $stmt->bindValue(1, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(2, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function store_count_all_active()
{
    $db = db_get_connection();
    return (int) $db->query('SELECT COUNT(*) FROM stores WHERE is_active = 1')->fetchColumn();
}

function store_search_paginated($query, $page = 1, $perPage = 50)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('SELECT * FROM stores WHERE is_active = 1 AND (name LIKE ? OR description LIKE ?) ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $stmt->bindValue(1, $like);
    $stmt->bindValue(2, $like);
    $stmt->bindValue(3, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(4, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function store_count_search($query)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $stmt = $db->prepare('SELECT COUNT(*) FROM stores WHERE is_active = 1 AND (name LIKE ? OR description LIKE ?)');
    $stmt->execute([$like, $like]);
    return (int) $stmt->fetchColumn();
}

/* Create a new store for an existing user. */

function store_create_for_user($ownerId, $name, $description, $contactPhone, $contactEmail = '')
{
    try {
        $db = db_get_connection();

        $slug = auth_create_slug($name);

        $slugExists = $db->prepare('SELECT id FROM stores WHERE slug = ?');
        $slugExists->execute([$slug]);
        if ($slugExists->fetch()) {
            return ['success' => false, 'error' => 'Store name already taken'];
        }

        $storeId = auth_generate_id();
        $stmt = $db->prepare('INSERT INTO stores (id, name, slug, description, owner_id, contact_phone, contact_email, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())');
        $stmt->execute([$storeId, $name, $slug, $description, $ownerId, $contactPhone, $contactEmail]);

        return [
            'success' => true,
            'storeId' => $storeId,
            'storeSlug' => $slug,
            'message' => 'Store created successfully'
        ];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/* Search active stores by name or description. */

function store_search($query)
{
    $db = db_get_connection();
    $like = '%' . $query . '%';
    $stmt = $db->prepare('SELECT * FROM stores WHERE is_active = 1 AND (name LIKE ? OR description LIKE ?) ORDER BY created_at DESC');
    $stmt->execute([$like, $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Load a store and its available products for public display. */

function store_get_with_products($slug)
{
    $store = store_get_by_slug($slug);
    if (!$store) {
        return null;
    }

    $db = db_get_connection();
    $stmt = $db->prepare('SELECT * FROM products WHERE store_id = ? AND is_available = 1 ORDER BY created_at DESC');
    $stmt->execute([$store['id']]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'store' => $store,
        'products' => $products,
    ];
}
