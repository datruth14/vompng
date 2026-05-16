<?php

namespace Backend;

class Store
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getStoreBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM stores WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getStoreBySlugForOwner($slug, $ownerId)
    {
        $stmt = $this->db->prepare("SELECT * FROM stores WHERE slug = ? AND owner_id = ? AND is_active = 1");
        $stmt->execute([$slug, $ownerId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getUserStores($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM stores WHERE owner_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStore($storeId, $data)
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
        ];
        $updateData = array_intersect_key($data, array_flip($allowed));
        
        if (empty($updateData)) {
            return ['success' => false, 'error' => 'No valid data to update'];
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');
        
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($updateData)));
        $sql = "UPDATE stores SET $set WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(array_merge(array_values($updateData), [$storeId]));

        return $result ? 
            ['success' => true, 'message' => 'Store updated'] :
            ['success' => false, 'error' => 'Failed to update store'];
    }

    public function getStoreWithProducts($slug)
    {
        $store = $this->getStoreBySlug($slug);
        if (!$store) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM products WHERE store_id = ? AND is_available = 1 ORDER BY created_at DESC");
        $stmt->execute([$store['id']]);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'store' => $store,
            'products' => $products,
        ];
    }
}
