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

        /* Handle file upload if present */
        if (!empty($_FILES['media'])) {
            $file = $_FILES['media'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowedTypes)) {
                echo json_encode(['success' => false, 'error' => 'Invalid image format. Use JPG, PNG, GIF, or WebP']);
                exit;
            }

            if ($file['size'] > $maxSize) {
                echo json_encode(['success' => false, 'error' => 'Image too large. Max 5MB']);
                exit;
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'error' => 'Upload failed: ' . $file['error']]);
                exit;
            }

            /* Create uploads directory if it doesn't exist */
            $uploadsDir = __DIR__ . '/../uploads';
            if (!is_dir($uploadsDir)) {
                if (!mkdir($uploadsDir, 0755, true)) {
                    echo json_encode(['success' => false, 'error' => 'Failed to create uploads directory']);
                    exit;
                }
            }

            /* Generate unique filename */
            $fileName = bin2hex(random_bytes(16)) . '.webp';
            $filePath = $uploadsDir . '/' . $fileName;

            /* Optimize and convert image to WebP if GD is available, otherwise save original */
            if (extension_loaded('gd')) {
                $optimized = optimize_image_to_webp($file['tmp_name'], $filePath);
                if (!$optimized) {
                    echo json_encode(['success' => false, 'error' => 'Failed to process image']);
                    exit;
                }
            } else {
                /* Fallback: save file with original extension and convert filename to webp reference */
                $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $fallbackFileName = bin2hex(random_bytes(16)) . '.' . $origExt;
                $fallbackPath = $uploadsDir . '/' . $fallbackFileName;
                
                if (!move_uploaded_file($file['tmp_name'], $fallbackPath)) {
                    echo json_encode(['success' => false, 'error' => 'Failed to save image']);
                    exit;
                }
                
                /* Use original file as fallback */
                $fileName = $fallbackFileName;
                $filePath = $fallbackPath;
            }

            /* Set media URL to the uploaded file path */
            $mediaUrl = '/uploads/' . $fileName;
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

/* Helper function to optimize and convert images to WebP format with compression */
function optimize_image_to_webp($sourcePath, $destPath)
{
    if (!extension_loaded('gd')) {
        return false;
    }

    try {
        /* Determine image type and create image resource */
        $imageType = exif_imagetype($sourcePath);
        
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        /* Get original dimensions */
        $origWidth = imagesx($image);
        $origHeight = imagesy($image);
        
        /* Calculate new dimensions (max 1200px on longest side) */
        $maxDim = 1200;
        $ratio = 1;
        if ($origWidth > $maxDim || $origHeight > $maxDim) {
            $ratio = min($maxDim / $origWidth, $maxDim / $origHeight);
        }
        
        $newWidth = (int)($origWidth * $ratio);
        $newHeight = (int)($origHeight * $ratio);

        /* Create resampled image */
        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        /* Preserve transparency for PNG and GIF */
        if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        /* Save as highly compressed WebP (quality 75 for good balance) */
        $quality = 75;
        $result = imagewebp($resized, $destPath, $quality);

        imagedestroy($image);
        imagedestroy($resized);

        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}
