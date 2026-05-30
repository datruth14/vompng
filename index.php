<?php
/*
 * Main application router and page controller.
 * Handles routing, authentication checks, and rendering front-end templates.
 */


require_once 'backend/Database.php';
require_once 'backend/Logger.php';
require_once 'backend/Auth.php';
require_once 'backend/Store.php';
require_once 'backend/Product.php';
require_once 'backend/Token.php';
require_once 'backend/Admin.php';

$currentUser = auth_get_current_user();

$error = null;
$success = null;
$content = '';
$pageTitle = 'vomp';

if (isset($_GET['error']) && $_GET['error'] !== '') {
    $error = $_GET['error'];
}

if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $success = 'Registration successful. Please log in.';
}

if (isset($_GET['success']) && $_GET['success'] !== '') {
    $success = $_GET['success'];
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestPath = trim(str_replace('/index.php', '', $uri), '/');
$requestPath = ltrim($requestPath, '/') ?: '';
// Fallback for Apache rewrites that set ?path= via .htaccess
if ($requestPath === '' && isset($_GET['path']) && $_GET['path'] !== '') {
    $requestPath = rtrim($_GET['path'], '/');
}
$segments = $requestPath === '' ? [] : explode('/', $requestPath);
$method = $_SERVER['REQUEST_METHOD'];

logger_info(sprintf('%s %s %s', $_SERVER['REMOTE_ADDR'] ?? '-', $method, $_SERVER['REQUEST_URI']));

if ($currentUser && in_array($requestPath, ['login', 'register', 'onboarding'], true)) {
    header('Location: /dashboard');
    exit;
}

if (
    !$currentUser &&
    (
        $requestPath === 'dashboard' ||
        str_starts_with($requestPath, 'dashboard/') ||
        $requestPath === 'orders' ||
        $requestPath === 'profile' ||
        $requestPath === 'tokens' ||
        str_starts_with($requestPath, 'api/products') ||
        str_starts_with($requestPath, 'api/settings') ||
        str_starts_with($requestPath, 'api/tokens/purchase') ||
        str_starts_with($requestPath, 'api/tokens_purchase.php') ||
        str_starts_with($requestPath, 'api/tokens/transfer') ||
        str_starts_with($requestPath, 'api/tokens_transfer.php') ||
        str_starts_with($requestPath, 'api/store/') ||
        str_starts_with($requestPath, 'api/upgrade')
    )
) {
    header('Location: /login');
    exit;
}

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

        case $requestPath === 'forgot-password':
            include 'frontend/forgot_password.php';
            break;

        case $requestPath === 'reset-password':
            if (empty($_GET['email'])) {
                header('Location: /forgot-password');
                exit;
            }
            include 'frontend/reset_password.php';
            break;

        case $requestPath === 'products':
            $searchQuery = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : null;
            $activeCategory = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;
            $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
            $perPage = 12;
            $categories = product_get_categories();
            if ($searchQuery) {
                $products = product_search_paginated($searchQuery, $page, $perPage);
                $totalProducts = product_count_search($searchQuery);
            } elseif ($activeCategory) {
                $products = product_get_by_category_paginated($activeCategory, $page, $perPage);
                $totalProducts = product_count_by_category($activeCategory);
            } else {
                $products = product_get_all_available_paginated($page, $perPage);
                $totalProducts = product_count_all_available();
            }
            $totalPages = max(1, (int) ceil($totalProducts / $perPage));
            include 'frontend/products.php';
            break;

        case $requestPath === 'stores':
            $searchQuery = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : null;
            $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
            $perPage = 9;
            if ($searchQuery) {
                $stores = store_search_paginated($searchQuery, $page, $perPage);
                $totalStores = store_count_search($searchQuery);
            } else {
                $stores = store_get_top_active_paginated($page, $perPage);
                $totalStores = store_count_top_active();
            }
            $totalPages = max(1, (int) ceil($totalStores / $perPage));
            include 'frontend/stores.php';
            break;

        case $requestPath === 'marketplace':
            $searchQuery = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : null;
            $stores = store_get_top_active_paginated(1, 6);
            $categories = product_get_categories();
            $activeCategory = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;

            if ($searchQuery) {
                $allProducts = product_search_paginated($searchQuery, 1, 8);
                $searchStores = store_search_paginated($searchQuery, 1, 8);
            } elseif ($activeCategory) {
                $allProducts = product_get_by_category_paginated($activeCategory, 1, 8);
                $searchStores = null;
            } else {
                $allProducts = product_get_all_available_paginated(1, 8);
                $searchStores = null;
            }
            include 'frontend/marketplace.php';
            break;

        case $requestPath === 'orders':
            $stores = store_get_user_stores($currentUser['id']);
            include 'frontend/orders.php';
            break;

        case $requestPath === 'download':
            $pageTitle = 'Download App - vomp';
            ob_start();
            ?>
            <section class="min-h-[60vh] flex items-center justify-center">
                <div class="text-center space-y-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-[#ff610a]/10 border border-[#ff610a]/20 mb-4">
                        <svg class="w-10 h-10 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black text-white tracking-tight">Coming Soon</h1>
                    <p class="text-gray-400 text-lg max-w-md mx-auto">The vomp mobile app is on its way. You'll be able to manage your store on the go.</p>
                </div>
            </section>
            <?php
            $content = ob_get_clean();
            break;

        case $requestPath === 'profile':
            include 'frontend/profile.php';
            break;

        case $requestPath === 'tokens':
            $userStores = store_get_user_stores($currentUser['id']);
            if ($userStores) {
                header('Location: /dashboard/' . rawurlencode($userStores[0]['slug']) . '/tokens');
                exit;
            }
            $transactions = token_user_history($currentUser['id']);
            include 'frontend/dashboard_tokens.php';
            break;

        case $requestPath === 'dashboard/create-store':
            $userStores = store_get_user_stores($currentUser['id']);
            if (($currentUser['plan'] ?? 'free') !== 'premium' && count($userStores) > 0) {
                header('Location: /dashboard');
                exit;
            }
            $defaultPhone = $userStores ? $userStores[0]['contact_phone'] : ($currentUser['phone'] ?? '');
            $defaultEmail = $userStores ? $userStores[0]['contact_email'] : $currentUser['email'];
            include 'frontend/create_store.php';
            break;

        case $requestPath === 'dashboard':
            $stores = store_get_user_stores($currentUser['id']);
            $totalProducts = 0;
            foreach ($stores as $s) {
                $totalProducts += count(product_get_products_by_store($s['id']));
            }
            include 'frontend/dashboard.php';
            break;

        case $requestPath === 'dashboard/stores':
            $stores = store_get_user_stores($currentUser['id']);
            include 'frontend/dashboard_stores.php';
            break;

        case $requestPath === 'dashboard/products':
            $stores = store_get_user_stores($currentUser['id']);
            $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
            $perPage = 12;
            $products = product_get_by_user_id_paginated($currentUser['id'], $page, $perPage);
            $totalProductsAll = product_count_by_user_id($currentUser['id']);
            $totalPages = max(1, (int) ceil($totalProductsAll / $perPage));
            include 'frontend/dashboard_products_all.php';
            break;

        case count($segments) === 2 && $segments[0] === 'dashboard':
            $storeSlug = $segments[1];
            $store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $products = product_get_products_by_store($store['id']);
            $transactions = token_history($store['id'], 5);
            include 'frontend/dashboard_overview.php';
            break;

        case count($segments) === 3 && $segments[0] === 'dashboard' && $segments[2] === 'products':
            $storeSlug = $segments[1];
            $store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $products = product_get_products_by_store($store['id']);
            $productCategories = product_get_categories();
            include 'frontend/dashboard_products.php';
            break;

        case count($segments) === 3 && $segments[0] === 'dashboard' && $segments[2] === 'settings':
            $storeSlug = $segments[1];
            $store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            include 'frontend/dashboard_settings.php';
            break;

        case count($segments) === 3 && $segments[0] === 'dashboard' && $segments[2] === 'tokens':
            $storeSlug = $segments[1];
            $store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $storeTransactions = token_history($store['id']);
            $userTransactions = token_user_history($currentUser['id']);
            $transactions = array_merge($storeTransactions, $userTransactions);
            usort($transactions, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
            include 'frontend/dashboard_tokens.php';
            break;

        case count($segments) === 3 && $segments[0] === 'store':
            $storeSlug = $segments[1];
            $productId = $segments[2];
            $store = store_get_by_slug($storeSlug);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $product = product_get_by_id_and_store($productId, $store['id']);
            if (!$product) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Product not found</h2></div>';
                break;
            }
            include 'frontend/product_detail.php';
            break;

        case count($segments) === 2 && $segments[0] === 'store':
            $storeSlug = $segments[1];
            $store = store_get_by_slug($storeSlug);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
            $perPage = 6;
            $products = product_get_available_products_by_store_paginated($store['id'], $page, $perPage);
            $totalProducts = product_count_available_by_store($store['id']);
            $totalPages = max(1, (int) ceil($totalProducts / $perPage));
            include 'frontend/storefront.php';
            break;

        case str_starts_with($requestPath, 'admin'):
            if (!auth_is_admin()) {
                http_response_code(403);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">403 - Forbidden</h2></div>';
                break;
            }

            switch ($requestPath) {
                case 'admin':
                    $totalUsers = admin_count_users();
                    $totalStores = admin_count_stores();
                    $totalProducts = admin_count_products();
                    $totalTransactions = admin_count_transactions();
                    include 'frontend/admin/dashboard.php';
                    break;

                case 'admin/users':
                    $searchQuery = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : null;
                    $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
                    $perPage = 20;
                    if ($searchQuery) {
                        $users = admin_search_users_paginated($searchQuery, $page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_search_users($searchQuery) / $perPage));
                    } else {
                        $users = admin_get_users_paginated($page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_users_total() / $perPage));
                    }
                    include 'frontend/admin/users.php';
                    break;

                case 'admin/stores':
                    $success = $_GET['success'] ?? null;
                    $error = $_GET['error'] ?? null;
                    $searchQuery = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : null;
                    $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
                    $perPage = 20;
                    if ($searchQuery) {
                        $stores = admin_search_stores_paginated($searchQuery, $page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_search_stores($searchQuery) / $perPage));
                    } else {
                        $stores = admin_get_stores_paginated($page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_stores_total() / $perPage));
                    }
                    include 'frontend/admin/stores.php';
                    break;

                case 'admin/products':
                    $searchQuery = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : null;
                    $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
                    $perPage = 20;
                    if ($searchQuery) {
                        $products = admin_search_products_paginated($searchQuery, $page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_search_products($searchQuery) / $perPage));
                    } else {
                        $products = admin_get_products_paginated($page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_products_total() / $perPage));
                    }
                    include 'frontend/admin/products.php';
                    break;

                case 'admin/orders':
                    $searchQuery = isset($_GET['q']) && $_GET['q'] !== '' ? trim($_GET['q']) : null;
                    $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
                    $perPage = 30;
                    if ($searchQuery) {
                        $transactions = admin_search_transactions_paginated($searchQuery, $page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_search_transactions($searchQuery) / $perPage));
                    } else {
                        $transactions = admin_get_transactions_paginated($page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_transactions_total() / $perPage));
                    }
                    include 'frontend/admin/orders.php';
                    break;

                default:
                    http_response_code(404);
                    $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">404 - Admin page not found</h2></div>';
            }
            break;

        case $requestPath === 'api/admin/export':
            include 'api/admin_export.php';
            exit;

        case $requestPath === 'logout':
            include 'api/logout.php';
            exit;

        case $requestPath === 'api/tokens/deduct':
            include 'api/tokens_deduct.php';
            exit;

        case $requestPath === 'api/tokens_verify.php':
            include 'api/tokens_verify.php';
            exit;

        default:
            http_response_code(404);
            $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">404 - Page Not Found</h2></div>';
    }
} else if ($method === 'POST') {
    switch ($requestPath) {
        case 'api/register':
        case 'api/register.php':
            include 'api/register.php';
            exit;
        case 'api/login':
        case 'api/login.php':
            include 'api/login.php';
            exit;
        case 'api/products':
        case 'api/products.php':
            include 'api/products.php';
            exit;
        case 'api/settings':
        case 'api/settings.php':
            include 'api/settings.php';
            exit;
        case 'api/tokens/purchase':
        case 'api/tokens/purchase.php':
        case 'api/tokens_purchase.php':
            include 'api/tokens_purchase.php';
            exit;
        case 'api/store-create':
        case 'api/store_create.php':
            include 'api/store_create.php';
            exit;
        case 'api/profile':
        case 'api/profile.php':
            include 'api/profile.php';
            exit;
        case 'api/upgrade':
        case 'api/upgrade.php':
            include 'api/upgrade.php';
            exit;
        case 'api/tokens/transfer':
        case 'api/tokens_transfer.php':
            include 'api/tokens_transfer.php';
            exit;
        case 'api/admin/toggle-store':
            include 'api/admin_toggle_store.php';
            exit;
        case 'api/forgot_password.php':
            include 'api/forgot_password.php';
            exit;
        case 'api/reset_password.php':
            include 'api/reset_password.php';
            exit;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
            exit;
    }
}

// Render layout
include 'frontend/layout.php';

