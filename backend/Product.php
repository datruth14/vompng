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
    $store = db_fetch('SELECT id, owner_id FROM stores WHERE id = ?', [$storeId]);
    if (!$store) return [];

    $stmt = $db->prepare('
        SELECT p.* FROM products p
        WHERE p.store_id = ? OR p.store_id = ?
        ORDER BY p.created_at DESC
    ');
    $stmt->execute([$store['id'], $store['owner_id']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Return only available products for a store. */

function product_get_available_products_by_store($storeId)
{
    $db = db_get_connection();
    $ownerId = store_owner_id($storeId);
    $stmt = $db->prepare('
        SELECT * FROM products
        WHERE (store_id = ? OR store_id = ?)
        AND is_available = 1
        ORDER BY created_at DESC
    ');
    $stmt->execute([$storeId, $ownerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_get_available_products_by_store_paginated($storeId, $page = 1, $perPage = 12)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $ownerId = store_owner_id($storeId);
    $stmt = $db->prepare('
        SELECT * FROM products
        WHERE (store_id = ? OR store_id = ?)
        AND is_available = 1
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, $storeId);
    $stmt->bindValue(2, $ownerId);
    $stmt->bindValue(3, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(4, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_count_available_by_store($storeId)
{
    $db = db_get_connection();
    $ownerId = store_owner_id($storeId);
    $stmt = $db->prepare('
        SELECT COUNT(*) FROM products
        WHERE (store_id = ? OR store_id = ?)
        AND is_available = 1
    ');
    $stmt->execute([$storeId, $ownerId]);
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
    $ownerId = store_owner_id($storeId);
    $stmt = $db->prepare('
        SELECT * FROM products
        WHERE id = ? AND (store_id = ? OR store_id = ?)
    ');
    $stmt->execute([$productId, $storeId, $ownerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* Create a new product record for a store. */

function product_create($storeId, $name, $price, $description = '', $mediaUrl = '', $mediaType = 'image', $category = '', $country = 'Nigeria', $state = '', $currency = 'NGN', $affiliateUrl = '')
{
    $db = db_get_connection();
    $id = bin2hex(random_bytes(12));

    $stmt = $db->prepare(
        'INSERT INTO products (id, name, price, description, media_url, media_type, category, store_id, country, state, currency, affiliate_url, is_available, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())'
    );

    $result = $stmt->execute([$id, $name, $price, $description, $mediaUrl, $mediaType, $category ?: 'Others', $storeId, $country, $state, $currency, $affiliateUrl]);

    return $result ? ['success' => true, 'id' => $id, 'message' => 'Product created'] : ['success' => false, 'error' => 'Failed to create product'];
}

/* Update fields for an existing product. */

function product_update($productId, $data)
{
    $allowed = ['name', 'price', 'description', 'media_url', 'media_type', 'is_available', 'category', 'country', 'state', 'currency', 'affiliate_url'];
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

/* Delete a specific product by ID and its associated image. */

function product_delete($productId)
{
    $db = db_get_connection();

    $fetch = $db->prepare('SELECT media_url FROM products WHERE id = ?');
    $fetch->execute([$productId]);
    $product = $fetch->fetch(PDO::FETCH_ASSOC);

    if ($product && !empty($product['media_url'])) {
        $path = __DIR__ . '/../' . ltrim($product['media_url'], '/');
        if (is_file($path)) {
            unlink($path);
        }
    }

    $stmt = $db->prepare('DELETE FROM products WHERE id = ?');
    $result = $stmt->execute([$productId]);

    return $result ? ['success' => true, 'message' => 'Product deleted'] : ['success' => false, 'error' => 'Failed to delete product'];
}

/* Return all products across all stores owned by a user. */

function product_get_by_user_id($userId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('
        SELECT p.*, s.name AS store_name, s.slug AS store_slug
        FROM products p
        JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        WHERE s.owner_id = ?
        ORDER BY p.created_at DESC
    ');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_get_by_user_id_paginated($userId, $page = 1, $perPage = 50)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $stmt = $db->prepare('
        SELECT p.*, s.name AS store_name, s.slug AS store_slug
        FROM products p
        JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        WHERE s.owner_id = ?
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, $userId);
    $stmt->bindValue(2, (int) $perPage, PDO::PARAM_INT);
    $stmt->bindValue(3, (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function product_count_by_user_id($userId)
{
    $db = db_get_connection();
    $stmt = $db->prepare('
        SELECT COUNT(*) FROM products p
        JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        WHERE s.owner_id = ?
    ');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

/* countries.dev API helper - fetches with file cache */
function _countries_dev_fetch($endpoint, $cacheFile, $ttl = 86400)
{
    $cachePath = __DIR__ . '/../cache/' . $cacheFile;

    if (is_file($cachePath) && (time() - filemtime($cachePath)) < $ttl) {
        $data = json_decode(file_get_contents($cachePath), true);
        if ($data !== null) return $data;
    }

    $url = 'https://countries.dev' . $endpoint;
    $ctx = stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'VomP/1.0']]);
    $response = @file_get_contents($url, false, $ctx);

    if ($response === false) {
        if (is_file($cachePath)) {
            $data = json_decode(file_get_contents($cachePath), true);
            if ($data !== null) return $data;
        }
        return null;
    }

    $data = json_decode($response, true);
    if ($data === null) return null;

    file_put_contents($cachePath, json_encode($data));
    return $data;
}

function product_get_countries()
{
    $data = _countries_dev_fetch('/countries?fields=name,alpha2Code,currencies&limit=250', 'countries.dev_countries.json');
    if ($data === null || !is_array($data)) {
        return ['Nigeria', 'Ghana', 'Kenya', 'South Africa', 'Uganda', 'Tanzania', 'Rwanda', 'Ethiopia', 'Egypt', 'Morocco', 'Zambia', 'Zimbabwe', 'Botswana', 'Namibia', 'Mozambique', 'Senegal', 'Ivory Coast', 'Cameroon', 'Angola', 'DRC'];
    }
    $names = array_map(fn($c) => $c['name'], $data);
    sort($names);
    return $names;
}

function product_get_countries_with_products()
{
    $db = db_get_connection();
    $stmt = $db->query("SELECT DISTINCT country FROM products WHERE country IS NOT NULL AND country != '' ORDER BY country ASC");
    return array_map(fn($r) => $r['country'], $stmt->fetchAll(PDO::FETCH_ASSOC));
}

function product_get_country_data()
{
    $data = _countries_dev_fetch('/countries?fields=name,alpha2Code,currencies&limit=250', 'countries.dev_countries.json');
    if ($data === null || !is_array($data)) return [];
    usort($data, fn($a, $b) => strcmp($a['name'], $b['name']));
    $result = [];
    foreach ($data as $c) {
        $currencyCode = '';
        if (!empty($c['currencies']) && is_array($c['currencies'])) {
            $currencyCode = $c['currencies'][0]['code'] ?? '';
        }
        $result[] = ['name' => $c['name'], 'alpha2Code' => $c['alpha2Code'], 'currencyCode' => $currencyCode];
    }
    return $result;
}

function product_get_currencies()
{
    $data = _countries_dev_fetch('/currencies', 'countries.dev_currencies.json');
    if ($data === null || !is_array($data)) {
        return ['NGN' => '₦ (NGN)', 'GHS' => 'GH₵ (GHS)', 'KES' => 'KSh (KES)', 'ZAR' => 'R (ZAR)', 'UGX' => 'USh (UGX)', 'TZS' => 'TSh (TZS)', 'RWF' => 'FRw (RWF)', 'ETB' => 'Br (ETB)', 'EGP' => 'E£ (EGP)', 'MAD' => 'MAD', 'ZMW' => 'ZK (ZMW)', 'USD' => '$ (USD)', 'EUR' => '€ (EUR)', 'GBP' => '£ (GBP)'];
    }
    $result = [];
    foreach ($data as $c) {
        $result[$c['code']] = ($c['symbol'] ?: $c['code']) . ' (' . $c['code'] . ')';
    }
    ksort($result);
    return $result;
}

function product_get_currency_symbol($code)
{
    $data = _countries_dev_fetch('/currencies', 'countries.dev_currencies.json');
    if ($data !== null && is_array($data)) {
        foreach ($data as $c) {
            if ($c['code'] === $code) return $c['symbol'] ?: $code;
        }
    }
    $map = ['NGN' => '₦', 'GHS' => 'GH₵', 'KES' => 'KSh', 'ZAR' => 'R', 'UGX' => 'USh', 'TZS' => 'TSh', 'RWF' => 'FRw', 'ETB' => 'Br', 'EGP' => 'E£', 'MAD' => 'MAD', 'ZMW' => 'ZK', 'USD' => '$', 'EUR' => '€', 'GBP' => '£'];
    return $map[$code] ?? $code;
}

function product_get_by_country_currency_paginated($country = null, $currency = null, $page = 1, $perPage = 50)
{
    $db = db_get_connection();
    $offset = max(0, ($page - 1) * $perPage);
    $conditions = [];
    $params = [];
    if ($country) {
        $conditions[] = 'p.country = ?';
        $params[] = $country;
    }
    if ($currency) {
        $conditions[] = 'p.currency = ?';
        $params[] = $currency;
    }
    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $params[] = (int) $perPage;
    $params[] = (int) $offset;
    $stmt = $db->prepare("
        SELECT p.*, s.name AS store_name, s.slug AS store_slug, s.contact_phone
        FROM products p
        LEFT JOIN stores s ON p.store_id = s.id OR p.store_id = s.owner_id
        $where
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
