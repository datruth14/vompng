<?php

function db_load_env()
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

db_load_env();

require_once __DIR__ . '/../config/database.php';

function db_get_connection()
{
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $config = require __DIR__ . '/../config/database.php';

    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
        $db = new PDO(
            $dsn,
            $config['username'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        db_init_schema($db);
    } catch (PDOException $e) {
        error_log('Database connection error: ' . $e->getMessage());
        throw $e;
    }

    return $db;
}

function db_init_schema(PDO $db)
{
    $tables = [
        'users' => "
            CREATE TABLE IF NOT EXISTS users (
                id VARCHAR(24) PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        'stores' => "
            CREATE TABLE IF NOT EXISTS stores (
                id VARCHAR(24) PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                description TEXT,
                owner_id VARCHAR(24) NOT NULL,
                contact_phone VARCHAR(50),
                contact_email VARCHAR(255),
                logo_url TEXT,
                hero_image_url TEXT,
                hero_color VARCHAR(20) DEFAULT '#4f46e5',
                accent_color VARCHAR(20) DEFAULT '#8b5cf6',
                is_active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        'products' => "
            CREATE TABLE IF NOT EXISTS products (
                id VARCHAR(24) PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                description TEXT,
                media_url TEXT,
                media_type VARCHAR(20) DEFAULT 'image',
                is_available TINYINT(1) DEFAULT 1,
                category VARCHAR(100) DEFAULT 'Others',
                product_condition VARCHAR(50) DEFAULT 'Brand-New',
                location VARCHAR(255),
                store_id VARCHAR(24) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        'sessions' => "
            CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(24) PRIMARY KEY,
                user_id VARCHAR(24) NOT NULL,
                token VARCHAR(64) UNIQUE NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        'token_transactions' => "
            CREATE TABLE IF NOT EXISTS token_transactions (
                id VARCHAR(24) PRIMARY KEY,
                store_id VARCHAR(24) NULL,
                user_id VARCHAR(24) NULL,
                type VARCHAR(20) NOT NULL,
                amount INT NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        'withdrawals' => "
            CREATE TABLE IF NOT EXISTS withdrawals (
                id VARCHAR(24) PRIMARY KEY,
                user_id VARCHAR(24) NOT NULL,
                amount INT NOT NULL,
                naira_amount INT NOT NULL,
                bank_name VARCHAR(255) NOT NULL,
                account_number VARCHAR(20) NOT NULL,
                account_name VARCHAR(255) NOT NULL,
                status VARCHAR(20) DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                processed_at DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        'product_categories' => "
            CREATE TABLE IF NOT EXISTS product_categories (
                id VARCHAR(24) PRIMARY KEY,
                name VARCHAR(100) UNIQUE NOT NULL,
                sort_order INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ",
        'password_resets' => "
            CREATE TABLE IF NOT EXISTS password_resets (
                id VARCHAR(24) PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                otp VARCHAR(6) NOT NULL,
                expires_at DATETIME NOT NULL,
                used TINYINT(1) DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_password_resets_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        "
    ];

    foreach ($tables as $table => $sql) {
        try {
            $db->exec($sql);
        } catch (PDOException $e) {
            // Table might already exist
        }
    }

    db_ensure_column($db, 'users', 'phone', 'VARCHAR(50)');
    db_ensure_column($db, 'users', 'token_balance', 'INT DEFAULT 0');
    db_ensure_column($db, 'users', 'plan', "VARCHAR(20) DEFAULT 'free'");
    db_ensure_column($db, 'users', 'role', "VARCHAR(20) DEFAULT 'user'");

    // Seed admin role for 14eter@gmail.com
    $adminEmail = '14eter@gmail.com';
    $adminCheck = $db->prepare("SELECT id, role FROM users WHERE email = ?");
    $adminCheck->execute([$adminEmail]);
    $adminUser = $adminCheck->fetch(PDO::FETCH_ASSOC);
    if ($adminUser && $adminUser['role'] !== 'admin') {
        $db->prepare("UPDATE users SET role = 'admin' WHERE email = ?")->execute([$adminEmail]);
    }

    $migrated = $db->query('SELECT COUNT(*) FROM users WHERE token_balance > 0')->fetchColumn();
    if ((int) $migrated === 0) {
        $db->exec('UPDATE users SET token_balance = (SELECT COALESCE(SUM(token_balance), 0) FROM stores WHERE stores.owner_id = users.id)');
        $db->exec("UPDATE users SET plan = 'premium' WHERE id IN (SELECT owner_id FROM stores WHERE plan = 'premium')");
    }

    db_ensure_column($db, 'stores', 'contact_email', 'VARCHAR(255)');
    db_ensure_column($db, 'stores', 'logo_url', 'TEXT');
    db_ensure_column($db, 'stores', 'hero_image_url', 'TEXT');
    db_ensure_column($db, 'stores', 'hero_color', "VARCHAR(20) DEFAULT '#4f46e5'");
    db_ensure_column($db, 'stores', 'accent_color', "VARCHAR(20) DEFAULT '#8b5cf6'");
    db_ensure_column($db, 'stores', 'social_facebook', 'VARCHAR(255)');
    db_ensure_column($db, 'stores', 'social_instagram', 'VARCHAR(255)');
    db_ensure_column($db, 'stores', 'social_twitter', 'VARCHAR(255)');
    db_ensure_column($db, 'stores', 'social_tiktok', 'VARCHAR(255)');
    db_ensure_column($db, 'stores', 'social_youtube', 'VARCHAR(255)');

    db_ensure_column($db, 'products', 'media_url', 'TEXT');
    db_ensure_column($db, 'products', 'media_type', "VARCHAR(20) DEFAULT 'image'");
    db_ensure_column($db, 'products', 'is_available', "TINYINT(1) DEFAULT 1");
    db_ensure_column($db, 'products', 'category', "VARCHAR(100) DEFAULT 'Others'");
    db_ensure_column($db, 'products', 'product_condition', "VARCHAR(50) DEFAULT 'Brand-New'");
    db_ensure_column($db, 'products', 'location', 'VARCHAR(255)');

    // Migration: make token_transactions.store_id nullable and add user_id column
    try {
        $db->exec("ALTER TABLE token_transactions MODIFY store_id VARCHAR(24) NULL");
    } catch (PDOException $e) {
        // May fail if foreign key exists; try dropping FK first
        try {
            $fk = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'token_transactions' AND COLUMN_NAME = 'store_id' AND REFERENCED_TABLE_NAME IS NOT NULL")->fetchColumn();
            if ($fk) {
                $db->exec("ALTER TABLE token_transactions DROP FOREIGN KEY `$fk`");
            }
        } catch (PDOException $e2) {}
        try {
            $db->exec("ALTER TABLE token_transactions MODIFY store_id VARCHAR(24) NULL");
        } catch (PDOException $e2) {}
    }
    db_ensure_column($db, 'token_transactions', 'user_id', 'VARCHAR(24) NULL');

    $indexes = [
        'idx_products_store_id' => 'CREATE INDEX idx_products_store_id ON products(store_id)',
        'idx_stores_owner_id' => 'CREATE INDEX idx_stores_owner_id ON stores(owner_id)',
        'idx_tokens_store_id' => 'CREATE INDEX idx_tokens_store_id ON token_transactions(store_id)',
        'idx_tokens_user_id' => 'CREATE INDEX idx_tokens_user_id ON token_transactions(user_id)',
    ];
    foreach ($indexes as $name => $sql) {
        try {
            $db->exec($sql);
        } catch (PDOException $e) {
            // Index might already exist
        }
    }

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

function db_ensure_column(PDO $db, $table, $column, $definition)
{
    try {
        $query = $db->query("SHOW COLUMNS FROM `$table`");
        $columns = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $existing) {
            if (($existing['Field'] ?? '') === $column) {
                return;
            }
        }
        $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    } catch (PDOException $e) {
        // Ignore alter issues for existing installations.
    }
}

function db_query($sql, $params = [])
{
    $db = db_get_connection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_insert($table, $data)
{
    $db = db_get_connection();
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));

    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $db->prepare($sql);
    return $stmt->execute(array_values($data));
}

function db_update($table, $data, $where)
{
    $db = db_get_connection();
    $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
    $whereClause = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($where)));

    $sql = "UPDATE $table SET $set WHERE $whereClause";
    $stmt = $db->prepare($sql);
    return $stmt->execute(array_merge(array_values($data), array_values($where)));
}

function db_fetch($sql, $params = [])
{
    $stmt = db_query($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function db_fetch_all($sql, $params = [])
{
    $stmt = db_query($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function db_last_insert_id()
{
    return db_get_connection()->lastInsertId();
}

function img_url($url)
{
    if ($url && $url[0] !== '/') {
        return '/' . $url;
    }
    return $url;
}
