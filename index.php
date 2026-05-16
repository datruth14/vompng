<?php

// Autoload classes
require_once 'backend/Database.php';
require_once 'backend/Auth.php';
require_once 'backend/Store.php';
require_once 'backend/Product.php';
require_once 'backend/Token.php';
require_once 'backend/Logger.php';

use Backend\Auth;
use Backend\Store;
use Backend\Product;
use Backend\Token;

// Initialize auth
$auth = new Auth();
$currentUser = $auth->getCurrentUser();

// Initialize variables
$error = null;
$success = null;
$content = '';
$pageTitle = 'VomP';

if (isset($_GET['error']) && $_GET['error'] !== '') {
    $error = $_GET['error'];
}

if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $success = 'Registration successful. Please log in.';
}

// Get request path from URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestPath = trim(str_replace('/index.php', '', $uri), '/');
$requestPath = ltrim($requestPath, '/') ?: '';
$segments = $requestPath === '' ? [] : explode('/', $requestPath);
$method = $_SERVER['REQUEST_METHOD'];

\Backend\Logger::info(sprintf('%s %s %s', $_SERVER['REMOTE_ADDR'] ?? '-', $method, $_SERVER['REQUEST_URI']));

$storeModel = new Store();
$productModel = new Product();
$tokenModel = new Token();

// Normalize auth guards for private route families.
if (
    !$currentUser &&
    (
        $requestPath === 'dashboard' ||
        str_starts_with($requestPath, 'dashboard/') ||
        str_starts_with($requestPath, 'api/products') ||
        str_starts_with($requestPath, 'api/settings') ||
        str_starts_with($requestPath, 'api/tokens/purchase')
    )
) {
    header('Location: /login');
    exit;
}

// Handle 404 errors
http_response_code(200);

// Routes
if ($method === 'GET') {
    switch (true) {
        case $requestPath === '':
            include 'frontend/home.php';
            break;

        case $requestPath === 'onboarding':
        case $requestPath === 'register':
            include 'frontend/onboarding.php';
            break;

        case $requestPath === 'login':
            include 'frontend/login.php';
            break;

        case $requestPath === 'dashboard':
            $stores = $storeModel->getUserStores($currentUser['id']);
            $totalProducts = 0;
            foreach ($stores as $s) {
                $totalProducts += count($productModel->getProductsByStore($s['id']));
            }
            include 'frontend/dashboard.php';
            break;

        case count($segments) === 2 && $segments[0] === 'dashboard':
            $storeSlug = $segments[1];
            $store = $storeModel->getStoreBySlugForOwner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $products = $productModel->getProductsByStore($store['id']);
            $transactions = $tokenModel->history($store['id'], 5);
            include 'frontend/dashboard_overview.php';
            break;

        case count($segments) === 3 && $segments[0] === 'dashboard' && $segments[2] === 'products':
            $storeSlug = $segments[1];
            $store = $storeModel->getStoreBySlugForOwner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $products = $productModel->getProductsByStore($store['id']);
            include 'frontend/dashboard_products.php';
            break;

        case count($segments) === 3 && $segments[0] === 'dashboard' && $segments[2] === 'settings':
            $storeSlug = $segments[1];
            $store = $storeModel->getStoreBySlugForOwner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            include 'frontend/dashboard_settings.php';
            break;

        case count($segments) === 3 && $segments[0] === 'dashboard' && $segments[2] === 'tokens':
            $storeSlug = $segments[1];
            $store = $storeModel->getStoreBySlugForOwner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $transactions = $tokenModel->history($store['id']);
            $plans = $tokenModel->getPlans();
            include 'frontend/dashboard_tokens.php';
            break;

        case count($segments) === 2 && $segments[0] === 'store':
            $storeSlug = $segments[1];
            $storeBundle = $storeModel->getStoreWithProducts($storeSlug);
            if (!$storeBundle) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $store = $storeBundle['store'];
            $products = $storeBundle['products'];
            include 'frontend/storefront.php';
            break;

        case $requestPath === 'logout':
            include 'api/logout.php';
            exit;

        case $requestPath === 'api/tokens/deduct':
            include 'api/tokens_deduct.php';
            exit;

        default:
            http_response_code(404);
            $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">404 - Page Not Found</h2></div>';
    }
} else if ($method === 'POST') {
    switch ($requestPath) {
        case 'api/register':
            include 'api/register.php';
            exit;
        case 'api/login':
            include 'api/login.php';
            exit;
        case 'api/products':
            include 'api/products.php';
            exit;
        case 'api/settings':
            include 'api/settings.php';
            exit;
        case 'api/tokens/purchase':
            include 'api/tokens_purchase.php';
            exit;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
            exit;
    }
}

// Render layout
include 'frontend/layout.php';

