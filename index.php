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
        str_starts_with($requestPath, 'api/tokens/withdraw') ||
        str_starts_with($requestPath, 'api/tokens_withdraw.php') ||
        str_starts_with($requestPath, 'api/save_bank_details.php') ||
        str_starts_with($requestPath, 'api/resolve_account') ||
        str_starts_with($requestPath, 'api/list_banks') ||
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
            <section class="py-10">
                <div class="text-center mb-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-[#ff610a]/10 border border-[#ff610a]/20 mb-4">
                        <svg class="w-10 h-10 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black text-white tracking-tight mb-4">Download VomP</h1>
                    <p class="text-gray-400 text-lg max-w-lg mx-auto">Install VomP on your device for the best experience. Works offline and loads instantly.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-white/5 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M17.523 16.435c-.533.777-1.02 1.492-1.754 1.508-.734.016-.97-.437-1.807-.437-.836 0-1.098.423-1.79.453-.692.03-1.22-.477-1.754-1.255-.953-1.357-1.682-3.835-.703-5.508.48-.82 1.336-1.345 2.26-1.359.706-.014 1.373.477 1.805.477.432 0 1.243-.59 2.097-.503.358.015 1.361.145 2.005 1.09-.052.033-1.198.7-1.185 2.087.013 1.66 1.458 2.21 1.474 2.22-.013.04-.23.786-.757 1.558M14.1 8.194c.431-.527.724-1.258.645-1.987-.625.026-1.38.417-1.83.943-.403.468-.756 1.217-.66 1.935.698.054 1.408-.356 1.845-.89"/><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-white mb-2">Android</h3>
                        <p class="text-gray-400 text-sm mb-6">Install from Chrome. Open VomP in Chrome, tap the menu (⋮) and select <strong class="text-white">Add to Home Screen</strong>.</p>
                        <button id="install-android" class="w-full px-6 py-4 rounded-2xl bg-[#ff610a] text-white font-black hover:bg-[#e05500] transition-all">Download</button>
                    </div>

                    <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-white/5 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.62-.71 1.64-1.23 2.59-1.2.13 1.04-.33 2.1-.93 2.84-.61.75-1.61 1.3-2.6 1.22-.14-.98.33-2.03.94-2.86"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-white mb-2">iOS</h3>
                        <p class="text-gray-400 text-sm mb-6">Open in Safari, tap the Share button <span class="text-white inline-block">⬆</span> and select <strong class="text-white">Add to Home Screen</strong>.</p>
                        <button id="install-ios" class="w-full px-6 py-4 rounded-2xl bg-[#ff610a] text-white font-black hover:bg-[#e05500] transition-all">Download</button>
                    </div>

                    <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center">
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-white/5 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                        </div>
                        <h3 class="text-xl font-black text-white mb-2">Desktop</h3>
                        <p class="text-gray-400 text-sm mb-6">In Chrome or Edge, click the install icon <span class="text-white inline-block">+</span> in the address bar or menu <strong class="text-white">Install VomP</strong>.</p>
                        <button id="install-desktop" class="w-full px-6 py-4 rounded-2xl bg-[#ff610a] text-white font-black hover:bg-[#e05500] transition-all">Download</button>
                    </div>
                </div>
            </section>

            <script>
            document.getElementById('install-android')?.addEventListener('click', window.triggerInstall || (() => window.open('/','_blank')));
            document.getElementById('install-desktop')?.addEventListener('click', window.triggerInstall || (() => window.open('/','_blank')));
            document.getElementById('install-ios')?.addEventListener('click', function() {
              const existing = document.getElementById('iosModal');
              if (existing) existing.remove();
              const overlay = document.createElement('div');
              overlay.className = 'fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm';
              overlay.id = 'iosModal';
              overlay.addEventListener('click', () => overlay.remove());
              const box = document.createElement('div');
              box.className = 'bg-gray-900 rounded-2xl p-6 border border-white/10 max-w-sm w-full mx-4 shadow-2xl';
              box.addEventListener('click', (e) => e.stopPropagation());
              box.innerHTML =
                '<div class="text-center mb-4">' +
                  '<div class="w-12 h-12 mx-auto mb-3 rounded-full bg-white/5 flex items-center justify-center">' +
                    '<svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.62-.71 1.64-1.23 2.59-1.2.13 1.04-.33 2.1-.93 2.84-.61.75-1.61 1.3-2.6 1.22-.14-.98.33-2.03.94-2.86"/></svg>' +
                  '</div>' +
                  '<p class="text-white font-black text-lg mb-1">Install on iPhone</p>' +
                  '<p class="text-gray-400 text-sm mb-4">Follow these steps to add VomP to your home screen:</p>' +
                '</div>' +
                '<div class="space-y-4">' +
                  '<div class="flex items-start gap-3 bg-white/5 rounded-xl p-3 border border-white/10">' +
                    '<span class="flex-shrink-0 w-6 h-6 rounded-full bg-[#ff610a] text-white text-xs font-black flex items-center justify-center">1</span>' +
                    '<div><p class="text-white text-sm font-bold">Open in Safari</p><p class="text-gray-400 text-xs">Make sure you are viewing this page in Safari, not another browser.</p></div>' +
                  '</div>' +
                  '<div class="flex items-start gap-3 bg-white/5 rounded-xl p-3 border border-white/10">' +
                    '<span class="flex-shrink-0 w-6 h-6 rounded-full bg-[#ff610a] text-white text-xs font-black flex items-center justify-center">2</span>' +
                    '<div><p class="text-white text-sm font-bold">Tap the Share button</p><p class="text-gray-400 text-xs">It is the square icon with an arrow pointing up at the bottom of the screen.</p></div>' +
                  '</div>' +
                  '<div class="flex items-start gap-3 bg-white/5 rounded-xl p-3 border border-white/10">' +
                    '<span class="flex-shrink-0 w-6 h-6 rounded-full bg-[#ff610a] text-white text-xs font-black flex items-center justify-center">3</span>' +
                    '<div><p class="text-white text-sm font-bold">Scroll down & tap Add to Home Screen</p><p class="text-gray-400 text-xs">Then tap "Add" in the top right corner to install VomP.</p></div>' +
                  '</div>' +
                '</div>' +
                '<button class="mt-5 w-full px-6 py-3 rounded-xl bg-[#ff610a] text-white font-black hover:bg-[#e05500] transition-all" onclick="this.closest(\'#iosModal\').remove()">Got it</button>';
              overlay.appendChild(box);
              document.body.appendChild(overlay);
            });
            </script>
            <?php
            $content = ob_get_clean();
            break;

        case $requestPath === 'game':
            $pageTitle = 'Game - vomp';
            ob_start();
            ?>
            <section class="py-20 text-center">
                <div class="max-w-lg mx-auto">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-[#ff610a]/10 border border-[#ff610a]/20 mb-6">
                        <svg class="w-12 h-12 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" /></svg>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black text-white tracking-tight mb-4">Coming Soon</h1>
                    <p class="text-gray-400 text-lg">Something fun is on the way. Stay tuned!</p>
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
            require_once __DIR__ . '/backend/Order.php';
            $products = product_get_products_by_store($store['id']);
            $transactions = token_history($store['id'], 5);
            $orderCount = order_count_by_store($store['id']);
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

        case count($segments) === 3 && $segments[0] === 'dashboard' && $segments[2] === 'orders':
            $storeSlug = $segments[1];
            $store = store_get_by_slug_for_owner($storeSlug, $currentUser['id']);
            if (!$store) {
                http_response_code(404);
                $content = '<div class="text-center py-12"><h2 class="text-2xl font-bold">Store not found</h2></div>';
                break;
            }
            require_once __DIR__ . '/backend/Order.php';
            $page = max(1, isset($_GET['page']) ? (int) $_GET['page'] : 1);
            $perPage = 20;
            $from = $_GET['from'] ?? null;
            $to = $_GET['to'] ?? null;
            $result = order_get_by_store_paginated($store['id'], $page, $perPage, $from, $to);
            $orders = $result['orders'];
            $totalPages = $result['totalPages'];
            include 'frontend/dashboard_orders.php';
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
                    $commissionSummary = admin_commission_summary();
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
                        $withdrawals = admin_search_withdrawals_paginated($searchQuery, $page, $perPage);
                        $withdrawTotalPages = max(1, (int) ceil(admin_count_search_withdrawals($searchQuery) / $perPage));
                    } else {
                        $transactions = admin_get_transactions_paginated($page, $perPage);
                        $totalPages = max(1, (int) ceil(admin_count_transactions_total() / $perPage));
                        $withdrawals = admin_get_withdrawals_paginated($page, $perPage);
                        $withdrawTotalPages = max(1, (int) ceil(admin_count_withdrawals_total() / $perPage));
                    }
                    $commissionSummary = admin_commission_summary();
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
        case 'api/tokens/withdraw':
        case 'api/tokens_withdraw.php':
            include 'api/tokens_withdraw.php';
            exit;
        case 'api/save_bank_details.php':
            include 'api/save_bank_details.php';
            exit;
        case 'api/resolve_account.php':
            include 'api/resolve_account.php';
            exit;
        case 'api/list_banks.php':
            include 'api/list_banks.php';
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
        case 'api/tokens/deduct':
        case 'api/tokens_deduct.php':
            include 'api/tokens_deduct.php';
            exit;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
            exit;
    }
}

// Render layout
include 'frontend/layout.php';

