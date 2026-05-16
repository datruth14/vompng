<?php

namespace Backend;

class Database
{
    private $db;
    private static $instance = null;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';
        
        try {
            $this->db = new \PDO(
                'sqlite:' . $config['database'],
                null,
                null,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            
            // Create tables if they don't exist
            $this->createTables();
        } catch (\PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->db;
    }

    private function createTables()
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
                    token_balance INTEGER DEFAULT 50,
                    plan TEXT DEFAULT 'free',
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
            "
        ];

        foreach ($tables as $table => $sql) {
            try {
                $this->db->exec($sql);
            } catch (\PDOException $e) {
                // Table might already exist
            }
        }

        // Backfill schema for older local DB files.
        $this->ensureColumn('stores', 'contact_email', 'TEXT');
        $this->ensureColumn('stores', 'logo_url', 'TEXT');
        $this->ensureColumn('stores', 'hero_image_url', 'TEXT');
        $this->ensureColumn('stores', 'hero_color', "TEXT DEFAULT '#4f46e5'");
        $this->ensureColumn('stores', 'accent_color', "TEXT DEFAULT '#8b5cf6'");
        $this->ensureColumn('stores', 'token_balance', 'INTEGER DEFAULT 50');
        $this->ensureColumn('stores', 'plan', "TEXT DEFAULT 'free'");

        $this->ensureColumn('products', 'media_url', 'TEXT');
        $this->ensureColumn('products', 'media_type', "TEXT DEFAULT 'image'");
        $this->ensureColumn('products', 'is_available', 'INTEGER DEFAULT 1');

        $this->db->exec('CREATE INDEX IF NOT EXISTS idx_products_store_id ON products(store_id)');
        $this->db->exec('CREATE INDEX IF NOT EXISTS idx_stores_owner_id ON stores(owner_id)');
        $this->db->exec('CREATE INDEX IF NOT EXISTS idx_tokens_store_id ON token_transactions(store_id)');
    }

    private function ensureColumn($table, $column, $definition)
    {
        try {
            $query = $this->db->query("PRAGMA table_info($table)");
            $columns = $query->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($columns as $existing) {
                if (($existing['name'] ?? '') === $column) {
                    return;
                }
            }
            $this->db->exec("ALTER TABLE $table ADD COLUMN $column $definition");
        } catch (\PDOException $e) {
            // Ignore alter issues for existing installations.
        }
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function update($table, $data, $where)
    {
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $whereClause = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($where)));
        
        $sql = "UPDATE $table SET $set WHERE $whereClause";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_merge(array_values($data), array_values($where)));
    }

    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function lastInsertId()
    {
        return $this->db->lastInsertId();
    }
}
