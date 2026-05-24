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

            $errCodes = [
                1 => 'Image exceeds server upload limit',
                2 => 'Image exceeds form size limit',
                3 => 'Upload was incomplete',
                4 => 'No file was selected',
                6 => 'Server missing temp directory',
                7 => 'Server failed to write file',
            ];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'error' => $errCodes[$file['error']] ?? 'Upload error (' . $file['error'] . ')']);
                exit;
            }

            $maxSize = 30 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                echo json_encode(['success' => false, 'error' => 'Image too large. Max 30MB']);
                exit;
            }

            list($width, $height, $imageType) = getimagesize($file['tmp_name']);
            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF, IMAGETYPE_BMP];

            if (!in_array($imageType, $allowedTypes)) {
                echo json_encode(['success' => false, 'error' => 'Invalid image format. Use JPG, PNG, WebP, GIF or BMP']);
                exit;
            }

            if (!extension_loaded('gd')) {
                echo json_encode(['success' => false, 'error' => 'Server missing GD image library']);
                exit;
            }

            $targetDir = __DIR__ . '/../assets/media/images/product_images/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            if (!is_writable($targetDir)) {
                echo json_encode(['success' => false, 'error' => 'Upload directory not writable']);
                exit;
            }

            $cleanName = preg_replace("/[^a-zA-Z0-9_-]/", "", pathinfo($file['name'], PATHINFO_FILENAME));
            if (empty($cleanName)) $cleanName = "image";
            $uniqueName = time() . "_" . $cleanName . "_" . uniqid() . ".webp";
            $targetFile = $targetDir . $uniqueName;

            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($file['tmp_name']);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($file['tmp_name']);
                    break;
                case IMAGETYPE_WEBP:
                    $image = imagecreatefromwebp($file['tmp_name']);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($file['tmp_name']);
                    break;
                case IMAGETYPE_BMP:
                    $image = imagecreatefrombmp($file['tmp_name']);
                    break;
                default:
                    echo json_encode(['success' => false, 'error' => 'Unsupported image format']);
                    exit;
            }

            imagewebp($image, $targetFile, 60);
            imagedestroy($image);

            $mediaUrl = '/assets/media/images/product_images/' . $uniqueName;
        }

            if ($file['size'] > $maxSize) {
                echo json_encode(['success' => false, 'error' => 'Image too large. Max 10MB']);
                exit;
            }

            // Detect actual MIME type from file content
            $detectedType = '';
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detectedType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
            } elseif (function_exists('exif_imagetype')) {
                $exifType = exif_imagetype($file['tmp_name']);
                $exifMap = [
                    IMAGETYPE_JPEG => 'image/jpeg',
                    IMAGETYPE_PNG => 'image/png',
                    IMAGETYPE_GIF => 'image/gif',
                    IMAGETYPE_WEBP => 'image/webp',
                ];
                $detectedType = $exifMap[$exifType] ?? $file['type'];
            } else {
                $detectedType = $file['type'];
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/heic', 'image/heif'];
            if (!in_array($detectedType, $allowedTypes)) {
                echo json_encode(['success' => false, 'error' => 'Invalid image format. Use JPG, PNG, GIF, WebP, or HEIC']);
                exit;
            }

            /* Create uploads directory if it doesn't exist */
            $uploadsDir = __DIR__ . '/../assets/uploads';
            if (!is_dir($uploadsDir)) {
                if (!mkdir($uploadsDir, 0755, true)) {
                    echo json_encode(['success' => false, 'error' => 'Failed to create uploads directory']);
                    exit;
                }
            }

            /* Generate unique filename */
            $origExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = bin2hex(random_bytes(16)) . '.' . $origExt;
            $filePath = $uploadsDir . '/' . $fileName;

            /* Try to convert to WebP if GD supports it, otherwise save original */
            $converted = false;
            if (extension_loaded('gd') && in_array($detectedType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                $webpName = bin2hex(random_bytes(16)) . '.webp';
                $webpPath = $uploadsDir . '/' . $webpName;
                if (optimize_image_to_webp($file['tmp_name'], $webpPath)) {
                    $fileName = $webpName;
                    $filePath = $webpPath;
                    $converted = true;
                }
            }

            if (!$converted) {
                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    echo json_encode(['success' => false, 'error' => 'Failed to save image']);
                    exit;
                }
            }

            /* Set media URL to the uploaded file path */
            $mediaUrl = '/assets/uploads/' . $fileName;
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

