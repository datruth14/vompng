<?php
/*
 * API endpoint for product management.
 * Supports create, update, and delete actions for a store owner.
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Store.php';
require_once __DIR__ . '/../backend/Product.php';
require_once __DIR__ . '/../backend/Token.php';

header('Content-Type: application/json');

$currentUser = auth_get_current_user();

if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$storeSlug = $_GET['storeSlug'] ?? '';
$store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);

if (!$store) {
    http_response_code(404);
    echo json_encode(['error' => 'Store not found']);
    exit;
}

/* Handle multipart form data and JSON input interchangeably */
$data = [];
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $data = $_POST;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $action = $_GET['action'] ?? 'create';

    if ($action === 'create') {
        $name = $data['name'] ?? '';
        $price = $data['price'] ?? 0;
        $description = $data['description'] ?? '';
        $category = $data['category'] ?? '';
        $mediaUrl = $data['media_url'] ?? '';

        if (!$name) {
            echo json_encode(['success' => false, 'error' => 'Product name is required']);
            exit;
        }

        if (!empty($_FILES['media'])) {
            $file = $_FILES['media'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $map = [1 => 'exceeds server limit', 2 => 'exceeds form limit', 3 => 'incomplete', 4 => 'no file selected', 6 => 'missing temp dir', 7 => 'write failed'];
                echo json_encode(['success' => false, 'error' => 'Upload failed: ' . ($map[$file['error']] ?? 'error ' . $file['error'])]);
                exit;
            }

            if ($file['size'] > 30 * 1024 * 1024) {
                echo json_encode(['success' => false, 'error' => 'Image too large. Max 30MB']);
                exit;
            }

            if (!is_uploaded_file($file['tmp_name'])) {
                echo json_encode(['success' => false, 'error' => 'Invalid upload.']);
                exit;
            }

            $targetDir = __DIR__ . '/../assets/media/images/product_images/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $origName = pathinfo($file['name'], PATHINFO_FILENAME);
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $clean = preg_replace("/[^a-zA-Z0-9_-]/", "", $origName);
            if (empty($clean)) $clean = "image";
            $uniqueName = time() . "_" . $clean . "_" . uniqid() . "." . $ext;
            $targetFile = $targetDir . $uniqueName;

            $imgInfo = getimagesize($file['tmp_name']);
            if (!$imgInfo) {
                echo json_encode(['success' => false, 'error' => 'Failed to read image.']);
                exit;
            }
            $imageType = $imgInfo[2];

            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($file['tmp_name']);
                    imagejpeg($image, $targetFile, 30);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($file['tmp_name']);
                    imagepng($image, $targetFile, 9);
                    break;
                case IMAGETYPE_WEBP:
                    $image = imagecreatefromwebp($file['tmp_name']);
                    imagewebp($image, $targetFile, 30);
                    break;
                default:
                    echo json_encode(['success' => false, 'error' => 'Unsupported image format.']);
                    exit;
            }

            imagedestroy($image);
            $mediaUrl = '/assets/media/images/product_images/' . $uniqueName;
        }

        /* Deduct 10 tokens from user's central balance for publishing this product */
        $tokenResult = token_deduct_for_product_upload($currentUser['id'], $store['id']);
        if (!$tokenResult['success']) {
            http_response_code(402);
            echo json_encode(['success' => false, 'error' => $tokenResult['error'], 'code' => $tokenResult['code'] ?? null]);
            exit;
        }

        $result = product_create($store['id'], $name, $price, $description, $mediaUrl, 'image', $category);
        echo json_encode($result);
        exit;
    }

    if ($action === 'update') {
        $productId = $_GET['id'] ?? '';
        if (!$productId) {
            echo json_encode(['success' => false, 'error' => 'Product ID is required']);
            exit;
        }

        // Verify product belongs to store
        $products = product_get_products_by_store($store['id']);
        $owned = false;
        foreach ($products as $p) {
            if ($p['id'] === $productId) {
                $owned = true;
                break;
            }
        }

        if (!$owned) {
            echo json_encode(['success' => false, 'error' => 'Product not found in this store']);
            exit;
        }

        $result = product_update($productId, $data);
        echo json_encode($result);
        exit;
    }

    if ($action === 'delete') {
        $productId = $_GET['id'] ?? '';
        if (!$productId) {
            echo json_encode(['success' => false, 'error' => 'Product ID is required']);
            exit;
        }

        // Verify product belongs to store
        $products = product_get_products_by_store($store['id']);
        $owned = false;
        foreach ($products as $p) {
            if ($p['id'] === $productId) {
                $owned = true;
                break;
            }
        }

        if (!$owned) {
            echo json_encode(['success' => false, 'error' => 'Product not found in this store']);
            exit;
        }

        $result = product_delete($productId);
        echo json_encode($result);
        exit;
    }
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);

