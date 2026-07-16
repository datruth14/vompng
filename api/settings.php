<?php
/*
 * API endpoint for store settings.
 * Returns store details and applies updates for the authenticated owner.
 */

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';
require_once __DIR__ . '/../backend/Store.php';

header('Content-Type: application/json');

$user = auth_get_current_user();
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$slug = $_GET['storeSlug'] ?? null;
if (!$slug) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'storeSlug required']);
    exit;
}

$store = store_get_by_slug_for_owner($slug, $user['id']);
if (!$store) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Store not found or not owned by you']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode(['success' => true, 'store' => $store]);
    exit;
}

if ($method === 'POST') {
    /* Handle multipart form data and JSON input interchangeably */
    $data = [];
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $data = $_POST;
    }

    $allowed = [
        'name', 'description', 'contact_phone', 'contact_email',
        'social_facebook', 'social_instagram', 'social_twitter',
        'social_tiktok', 'social_youtube',
        'is_active'
    ];

    $toUpdate = [];
    foreach ($allowed as $k) {
        if (array_key_exists($k, $data)) {
            $toUpdate[$k] = $data[$k];
        }
    }

    /* Handle file upload for hero image if present */
    if (!empty($_FILES['hero_image'])) {
        $file = $_FILES['hero_image'];
        $maxSize = 10 * 1024 * 1024; // 10MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'Upload failed: ' . $file['error']]);
            exit;
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

        /* Set hero image URL to the uploaded file path */
        $toUpdate['hero_image_url'] = '/assets/uploads/' . $fileName;
    }

    $res = store_update($store['id'], $toUpdate);
    if ($res['success']) {
        echo json_encode($res);
    } else {
        http_response_code(400);
        echo json_encode($res);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

/* Helper function to optimize and convert images to WebP format with compression */
function optimize_image_to_webp($sourcePath, $destPath)
{
    if (!extension_loaded('gd')) {
        return false;
    }

    try {
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

        $origWidth = imagesx($image);
        $origHeight = imagesy($image);
        
        $maxDim = 1200;
        $ratio = 1;
        if ($origWidth > $maxDim || $origHeight > $maxDim) {
            $ratio = min($maxDim / $origWidth, $maxDim / $origHeight);
        }
        
        $newWidth = (int)($origWidth * $ratio);
        $newHeight = (int)($origHeight * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_GIF) {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        $quality = 75;
        $result = imagewebp($resized, $destPath, $quality);

        imagedestroy($image);
        imagedestroy($resized);

        return $result !== false;
    } catch (Exception $e) {
        return false;
    }
}
