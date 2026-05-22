# VomP — Agent Guide

## Overview
VomP is a PHP-based marketplace platform where sellers create stores, list products, and receive orders via WhatsApp. It uses a token system where each product upload costs 10 tokens and each WhatsApp order costs 1 token.

## Tech Stack
- **Backend:** Vanilla PHP (no framework), SQLite via PDO
- **Frontend:** PHP template files, Tailwind CSS (CDN), vanilla JS
- **Database:** SQLite at `database/vomp.db` (auto-creates schema on first connection)
- **Auth:** Cookie-based sessions (SHA-256 hashed tokens, bcrypt passwords)

## Directory Structure
```
index.php          - Main router (GET/POST routing + auth guards)
router.php         - PHP built-in server router (static files bypass)
config/database.php - SQLite connection config
backend/
  Database.php     - PDO singleton, schema init, generic query helpers
  Auth.php         - Register, login, logout, get_current_user, update_user
  Store.php        - Store CRUD + lookup by slug/owner + search + multi-store
  Product.php      - Product CRUD + search + category filter
  Token.php        - Token plans, purchase, deduct for upload/order, transaction history
  Paystack.php     - Paystack payment gateway integration (initialize, verify)
  Logger.php       - Simple file logger
  prune_sessions.php - CLI script to clean expired sessions (cron)
frontend/
  layout.php       - Main HTML shell with nav, mobile menu, flash messages, FAB side drawer
  home.php         - Landing page (unauthenticated) with footer
  onboarding.php   - Registration/onboarding page
  register.php     - Registration form
  login.php        - Login page
  dashboard.php    - Dashboard landing (store list + stats)
  dashboard_overview.php - Per-store dashboard
  dashboard_products.php - Product management (CRUD UI)
  dashboard_settings.php - Store settings (branding, contact info, social media)
  dashboard_tokens.php - Token purchase + transaction history
  create_store.php - Create additional store for existing user (auto-fills phone/email)
  marketplace.php  - Public marketplace with product grid, categories, search, storefronts
  orders.php       - Token activity with date filtering
  profile.php      - Edit user name, email, password (current password required to change)
  storefront.php   - Public storefront (hero, new products, store footer with social/contact)
  product_detail.php - Single product detail page with order form modal
api/
  register.php     - POST /api/register
  login.php        - POST /api/login
  logout.php       - POST /api/logout
  products.php     - POST /api/products (create/delete)
  settings.php     - POST /api/settings (update store)
  tokens_purchase.php - POST /api/tokens/purchase (initiates Paystack checkout)
  tokens_verify.php  - GET  /api/tokens_verify.php (Paystack callback, verifies & credits)
  tokens_deduct.php - POST /api/tokens/deduct (WhatsApp order, deducts 1 token)
  profile.php      - POST /api/profile (update user, requires current_password for password change)
  store_create.php  - POST /api/store_create.php (create additional store for logged-in user)
uploads/           - Uploaded product images
assets/            - theme.css + img/ (logo)
logs/              - app.log (rotated manually)
```

## Routing
All requests go through `index.php`. `router.php` serves static assets directly for the PHP built-in server. In production, `.htaccess` handles this.

Routes are in `index.php`:
- `GET /` → home page
- `GET /login`, `/register`, `/onboarding` → auth pages
- `GET /dashboard` → store list + stats
- `GET /dashboard/create-store` → create additional store (auto-fills phone/email)
- `GET /dashboard/{slug}` → store overview
- `GET /dashboard/{slug}/products|settings|tokens` → sub-pages
- `GET /marketplace` → browse products/stores (supports `?q=` search, `?category=` filter)
- `GET /orders` → token activity per store with date filtering
- `GET /profile` → edit user name/email/password
- `GET /store/{slug}` → public storefront with custom footer
- `GET /store/{slug}/{productId}` → product detail
- `POST /api/*` → API handlers

Auth guards redirect unauthenticated users to `/login` for dashboard, orders, profile, and their sub-routes. Authenticated users are redirected from login/register to `/dashboard`.

## Template Pattern
Every frontend template file follows this pattern:
```php
<?php
$pageTitle = '...';
ob_start();
?>
...HTML...
<?php
$content = ob_get_clean();
?>
```
The `index.php` router sets variables (e.g. `$store`, `$products`, `$transactions`), includes the template, then includes `layout.php` which echoes `$content`.

## Token System
- New stores get 50 free tokens
- Price: ₦20 per token (minimum purchase: 50 tokens for ₦1,000)
- Users enter a custom amount, price auto-calculates
- **Each product upload costs 10 tokens** (via `token_deduct_for_product_upload()`)
- **Each WhatsApp order costs 1 token** (via `token_deduct_for_order()` — atomic UPDATE + rowCount check)
- Tokens purchased sets plan to `'premium'` (upgrade from `'free'`)
- Constants: `TOKEN_PRICE_PER_UNIT` (20), `TOKEN_MINIMUM` (50) in `backend/Token.php`

## Database Schema (auto-created in `db_init_schema`)
- `users` (id, email, password, name, created_at, updated_at)
- `stores` (id, name, slug, description, owner_id, contact_phone, contact_email, logo_url, hero_image_url, hero_color, accent_color, token_balance, plan, is_active, social_facebook, social_instagram, social_twitter, social_tiktok, social_youtube, timestamps)
- `products` (id, name, price, description, media_url, media_type, is_available, category, product_condition, location, store_id, timestamps)
- `sessions` (id, user_id, token hash, expires_at, created_at)
- `token_transactions` (id, store_id, type, amount, description, created_at)

IDs are generated with `bin2hex(random_bytes(12))` (24-char hex).

## API Endpoints (POST)
| Endpoint | Action |
|---|---|
| `/api/register` | Register user + create store |
| `/api/login` | Authenticate & set session cookie |
| `/api/products?storeSlug=X&action=create` | Create product (multipart, file upload) |
| `/api/products?storeSlug=X&action=delete&id=Y` | Delete product |
| `/api/settings?storeSlug=X` | Update store settings (including social media) |
| `/api/tokens/purchase` | Initialize Paystack checkout for token purchase (storeSlug in query, tokens count in POST body) |
| `/api/tokens/deduct` | Generate WhatsApp order URL, deducts 1 token |
| `/api/profile` | Update user name/email/password |
| `/api/store_create.php` | Create additional store for existing user |

## Key Backend Functions

### Store.php
- `store_get_all_active()` — all active stores
- `store_search($query)` — search stores by name/description
- `store_get_by_slug($slug)` — single store with all columns (includes social fields)
- `store_get_with_products($slug)` — store + available products
- `store_get_by_slug_for_owner($slug, $ownerId)` — owner-only lookup
- `store_get_user_stores($userId)` — all stores owned by a user
- `store_create_for_user($ownerId, $name, $description, $contactPhone, $contactEmail)` — create additional store for existing user (50 free tokens)
- `store_update($storeId, $data)` — updates allowed fields including social_*

### Product.php
- `product_get_all_available()` — all available products across stores (JOINs store name/slug)
- `product_get_by_category($category)` — filter by category
- `product_get_categories()` — distinct category values
- `product_search($query)` — search by name, description, or store name
- `product_get_products_by_store($storeId)` / `product_get_available_products_by_store()`

### Token.php
- `token_purchase($storeId, $tokenCount)` — add tokens, upgrade plan to 'premium'
- `token_deduct_for_order($slug, $productId, $customer)` — deducts 1 token, returns WhatsApp URL
- `token_deduct_for_product_upload($storeId)` — deducts 10 tokens
- `token_history($storeId, $limit)` — recent transactions
- `token_history_by_date($storeId, $from, $to, $limit)` — transactions filtered by date range

### Auth.php
- `auth_register()` / `auth_login()` / `auth_logout()` / `auth_get_current_user()`
- `auth_update_user($userId, $data)` — update name, email, password (current_password must be verified in API before calling)

## Store Footer
Each storefront (`/store/{slug}`) shows a custom footer with:
- Store name + description
- Social Media column (Facebook, Instagram, TikTok, X/Twitter, YouTube — only those configured)
- Contact column (phone, email)
- Copyright with store name, powered by VomP
- Social links editable in Store Settings page under "Social Media Handles"

## Marketplace Page (`/marketplace`)
- Search bar (`?q=`) — searches products and stores
- Category pills (`?category=`) — filter by product category
- Product grid with image, price, name, store badge, location, condition
- "Explore Storefronts" section below products

## FAB Side Drawer
- Orange toggle button fixed on right edge of screen
- Opens a glassmorphism drawer from the right with Home, Marketplace, Orders, Profile links
- Logged-in users see Logout; guests see Register
- Toggle icon switches between hamburger and X

## Conventions
- All PHP functions are in the global namespace (no classes)
- Database queries use prepared statements via PDO
- User-facing strings use `htmlspecialchars()` for XSS prevention
- File uploads stored in `uploads/` with `uniqid` prefix
- UI uses glassmorphism via `glass-morphism` CSS class + Tailwind CDN
- Currency is Nigerian Naira (₦)
- Responsive design: mobile-first with `md:` breakpoints
- Button/accent color: `#ff610a` (orange)

## Token Purchase Flow (Paystack)
1. User enters token amount on `/dashboard/{slug}/tokens`, clicks "Buy Tokens"
2. Frontend POSTs to `/api/tokens_purchase.php?storeSlug=X` with `{ tokens: N }`
3. Backend calculates amount (tokens × ₦20 × 100 = kobo), calls Paystack initialize API
4. Paystack returns an `authorization_url` → frontend redirects browser there
5. User pays on Paystack checkout page
6. Paystack redirects back to `/api/tokens_verify.php?reference=REF&storeSlug=X`
7. Verify endpoint confirms payment with Paystack, credits tokens via `token_purchase()`
8. User is redirected to `/dashboard/{slug}/tokens?success=...`

## Paystack Config (`.env`)
```
PAYSTACK_SECRET_KEY=sk_test_your_paystack_secret_key
PAYSTACK_PUBLIC_KEY=pk_test_your_paystack_public_key
APP_URL=http://localhost:8000
```

## Key Backend Functions (updated)

### Paystack.php
- `paystack_load_config()` — loads `.env` into `getenv()` / `$_ENV`
- `paystack_secret_key()` / `paystack_public_key()` — return keys from env
- `paystack_initialize($email, $amountKobo, $metadata, $callbackUrl)` — creates a Paystack checkout transaction, returns `authorization_url`
- `paystack_verify($reference)` — verifies a completed Paystack transaction, returns `amount`, `reference`, `metadata`

## Files (updated)
- `backend/Paystack.php` — Paystack API functions (HTTP via `file_get_contents` + stream context, no curl dependency)
- `api/tokens_purchase.php` — Now initializes Paystack payment instead of directly crediting tokens
- `api/tokens_verify.php` — Paystack callback handler; verifies payment and credits tokens
- `frontend/dashboard_tokens.php` — Updated JS to redirect to Paystack checkout URL
- `.env` — Cleaned up, only contains Paystack keys and APP_URL

## Dev Server
```bash
php -S localhost:8000 router.php
```
