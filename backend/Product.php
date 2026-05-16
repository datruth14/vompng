<?php

namespace Backend;

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getProductsByStore($storeId)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE store_id = ? ORDER BY created_at DESC");
        $stmt->execute([$storeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAvailableProductsByStore($storeId)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE store_id = ? AND is_available = 1 ORDER BY created_at DESC");
        $stmt->execute([$storeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createProduct($storeId, $name, $price, $description = '', $mediaUrl = '', $mediaType = 'image')
    {
        $id = bin2hex(random_bytes(12));
        
        $stmt = $this->db->prepare("
            INSERT INTO products (
                id, name, price, description, media_url, media_type,
                store_id, is_available, created_at, updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, datetime('now'), datetime('now'))
        ");
        
        $result = $stmt->execute([$id, $name, $price, $description, $mediaUrl, $mediaType, $storeId]);
        
        return $result ?
            ['success' => true, 'id' => $id, 'message' => 'Product created'] :
            ['success' => false, 'error' => 'Failed to create product'];
    }

    public function updateProduct($productId, $data)
    {
        $allowed = ['name', 'price', 'description', 'media_url', 'media_type', 'is_available'];
        $updateData = array_intersect_key($data, array_flip($allowed));
        
        if (empty($updateData)) {
            return ['success' => false, 'error' => 'No valid data to update'];
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');
        
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($updateData)));
        $sql = "UPDATE products SET $set WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(array_merge(array_values($updateData), [$productId]));

        return $result ?
            ['success' => true, 'message' => 'Product updated'] :
            ['success' => false, 'error' => 'Failed to update product'];
    }

    public function deleteProduct($productId)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $result = $stmt->execute([$productId]);
        
        return $result ?
            ['success' => true, 'message' => 'Product deleted'] :
            ['success' => false, 'error' => 'Failed to delete product'];
    }
}
