<?php
/*
 * Database helper functions for the VOMP application.
 * Manages the SQLite connection, schema creation, and common query helpers.
 */


require_once __DIR__ . '/../config/database.php';

/* Return the singleton PDO connection for the application. */

function db_get_connection()
{
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $config = require __DIR__ . '/../config/database.php';

    try {
        $db = new PDO(
            'sqlite:' . $config['database'],
            null,
            null,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        db_init_schema($db);
    } catch (PDOException $e) {
        error_log('Database connection error: ' . $e->getMessage());
        throw $e;
    }

    return $db;
}

/* Ensure the database schema and tables exist. */

function db_init_schema(PDO $db)
{
    $tables = [
        'users' => "
            CREATE TABLE IF NOT EXISTS users (
                id TEXT PRIMARY KEY,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                name TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ",
        'stores' => "
            CREATE TABLE IF NOT EXISTS stores (
                id TEXT PRIMARY KEY,
                name TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                description TEXT,
                owner_id TEXT NOT NULL,
                contact_phone TEXT,
                contact_email TEXT,
                logo_url TEXT,
                hero_image_url TEXT,
                hero_color TEXT DEFAULT '#4f46e5',
                accent_color TEXT DEFAULT '#8b5cf6',
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ",
        'products' => "
            CREATE TABLE IF NOT EXISTS products (
                id TEXT PRIMARY KEY,
                name TEXT NOT NULL,
                price REAL NOT NULL,
                description TEXT,
                media_url TEXT,
                media_type TEXT DEFAULT 'image',
                is_available INTEGER DEFAULT 1,
                store_id TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
            )
        ",
        'sessions' => "
            CREATE TABLE IF NOT EXISTS sessions (
                id TEXT PRIMARY KEY,
                user_id TEXT NOT NULL,
                token TEXT UNIQUE NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ",
        'token_transactions' => "
            CREATE TABLE IF NOT EXISTS token_transactions (
                id TEXT PRIMARY KEY,
                store_id TEXT NOT NULL,
                type TEXT NOT NULL,
                amount INTEGER NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
            )
        ",
        'product_categories' => "
            CREATE TABLE IF NOT EXISTS product_categories (
                id TEXT PRIMARY KEY,
                name TEXT UNIQUE NOT NULL,
                sort_order INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        "
    ];

    foreach ($tables as $table => $sql) {
        try {
            $db->exec($sql);
        } catch (PDOException $e) {
            // Table might already exist
        }
    }

    db_ensure_column($db, 'users', 'token_balance', 'INTEGER DEFAULT 0');
    db_ensure_column($db, 'users', 'plan', "TEXT DEFAULT 'free'");

    // Migrate existing store token balances to user balances
    $migrated = $db->query('SELECT COUNT(*) FROM users WHERE token_balance > 0')->fetchColumn();
    if ((int) $migrated === 0) {
        $db->exec('UPDATE users SET token_balance = (SELECT COALESCE(SUM(token_balance), 0) FROM stores WHERE stores.owner_id = users.id)');
        $db->exec("UPDATE users SET plan = 'premium' WHERE id IN (SELECT owner_id FROM stores WHERE plan = 'premium')");
    }

    db_ensure_column($db, 'stores', 'contact_email', 'TEXT');
    db_ensure_column($db, 'stores', 'logo_url', 'TEXT');
    db_ensure_column($db, 'stores', 'hero_image_url', 'TEXT');
    db_ensure_column($db, 'stores', 'hero_color', "TEXT DEFAULT '#4f46e5'");
    db_ensure_column($db, 'stores', 'accent_color', "TEXT DEFAULT '#8b5cf6'");
    db_ensure_column($db, 'stores', 'social_facebook', 'TEXT');
    db_ensure_column($db, 'stores', 'social_instagram', 'TEXT');
    db_ensure_column($db, 'stores', 'social_twitter', 'TEXT');
    db_ensure_column($db, 'stores', 'social_tiktok', 'TEXT');
    db_ensure_column($db, 'stores', 'social_youtube', 'TEXT');

    db_ensure_column($db, 'products', 'media_url', 'TEXT');
    db_ensure_column($db, 'products', 'media_type', "TEXT DEFAULT 'image'");
    db_ensure_column($db, 'products', 'is_available', 'INTEGER DEFAULT 1');
    db_ensure_column($db, 'products', 'category', "TEXT DEFAULT 'Others'");
    db_ensure_column($db, 'products', 'product_condition', "TEXT DEFAULT 'Brand-New'");
    db_ensure_column($db, 'products', 'location', 'TEXT');

    $db->exec('CREATE INDEX IF NOT EXISTS idx_products_store_id ON products(store_id)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_stores_owner_id ON stores(owner_id)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_tokens_store_id ON token_transactions(store_id)');

    // Seed default categories if the table is empty
    $count = $db->query('SELECT COUNT(*) FROM product_categories')->fetchColumn();
    if ((int) $count === 0) {
        $defaults = [
            'Electronics', 'Fashion', 'Food & Groceries', 'Health & Beauty',
            'Home & Garden', 'Books', 'Sports', 'Kids', 'Automotive', 'Accessories'
        ];
        $stmt = $db->prepare('INSERT INTO product_categories (id, name, sort_order) VALUES (?, ?, ?)');
        foreach ($defaults as $i => $name) {
            $stmt->execute([bin2hex(random_bytes(12)), $name, $i]);
        }
    }
}

/* Add a column to a table if it is missing. */

function db_ensure_column(PDO $db, $table, $column, $definition)
{
    try {
        $query = $db->query("PRAGMA table_info($table)");
        $columns = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $existing) {
            if (($existing['name'] ?? '') === $column) {
                return;
            }
        }
        $db->exec("ALTER TABLE $table ADD COLUMN $column $definition");
    } catch (PDOException $e) {
        // Ignore alter issues for existing installations.
    }
}

/* Prepare and execute a SQL query, returning the PDO statement. */

function db_query($sql, $params = [])
{
    $db = db_get_connection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/* Insert a row into the specified table. */

function db_insert($table, $data)
{
    $db = db_get_connection();
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));

    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $db->prepare($sql);
    return $stmt->execute(array_values($data));
}

/* Update rows in the specified table based on a WHERE clause. */

function db_update($table, $data, $where)
{
    $db = db_get_connection();
    $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
    $whereClause = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($where)));

    $sql = "UPDATE $table SET $set WHERE $whereClause";
    $stmt = $db->prepare($sql);
    return $stmt->execute(array_merge(array_values($data), array_values($where)));
}

/* Execute a query and fetch a single row. */

function db_fetch($sql, $params = [])
{
    $stmt = db_query($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* Execute a query and fetch all rows. */

function db_fetch_all($sql, $params = [])
{
    $stmt = db_query($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* Return the last inserted row ID from the database. */

function db_last_insert_id()
{
    return db_get_connection()->lastInsertId();
}
