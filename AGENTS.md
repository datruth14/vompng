# VomP — Agent Guide

## Overview
VomP is a PHP-based marketplace platform where sellers create stores, list products, and receive orders via WhatsApp. It has evolved into a full platform with a token economy (Vomp Coins), bill payment services (VTU.NG), web games (GPTokens), store analytics, admin panel, and PWA support.

## Tech Stack
- **Backend:** Vanilla PHP (no framework), **MySQL** via PDO (migrated from SQLite)
- **Frontend:** PHP template files, Tailwind CSS (CDN), vanilla JS, Animate.css, Chart.js, TomSelect
- **Database:** MySQL via PDO (config in `.env`: DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT)
- **Auth:** Cookie-based sessions (SHA-256 hashed tokens, bcrypt passwords)
- **Image Processing:** GD library (JPEG quality 30, PNG 9, WebP 30) + client-side compression (WebP/JPEG, max 1600px, quality 70%)
- **Payments:** Paystack (token purchase + withdrawal payouts via Transfer API)
- **Bill Payments:** VTU.NG API v2 (JWT auth, file_get_contents, no cURL)
- **Email:** Resend API (raw HTTP, no SMTP/PHPMailer)
- **Games:** Canvas API (no frameworks), Web Audio API (programmatic sound)

## Directory Structure
```
index.php              - Main router (GET/POST routing + auth guards)
router.php             - PHP built-in server router (static files bypass)
.htaccess              - Apache rewrite rules (skips existing .php files)
.env                   - DB creds, Paystack keys, APP_URL, VTU.NG creds, Resend key
manifest.json          - PWA manifest
sw.js                  - Service worker
AGENTS.md              - This file
config/
  database.php         - (legacy SQLite config, unused)
backend/
  Database.php         - PDO singleton, schema init, db_ensure_column, generic helpers (db_query, db_insert, db_update, db_fetch_all, img_url)
  Auth.php             - Register, login, logout, get_current_user, update_user, verify_transaction_pin, is_admin
  Store.php            - Store CRUD + lookup by slug/owner + search
  Product.php          - Product CRUD + search + category filter
  Token.php            - Token purchase, transfer, withdraw, balance, transaction history (TOKEN_PRICE_PER_UNIT=20, TOKEN_MINIMUM=50)
  Paystack.php         - Paystack payment gateway + Transfer API (initialize, verify, list banks, resolve account, transfer)
  BillPayment.php      - All VTU.NG helpers + Vomp Coin deduction logic (BILL_COMMISSION_PERCENT=5)
  Order.php            - Order CRUD (vendor order tracking)
  Mailer.php           - Resend API wrapper (send OTP, notify withdrawal, notify bill payment)
  Admin.php            - Admin helpers (commission summary, paginated queries for users/stores/products/transactions/withdrawals/bill_payments)
  Logger.php           - Simple file logger
  prune_sessions.php   - CLI script to clean expired sessions (cron)
frontend/
  layout.php           - Main HTML shell, nav, FAB drawer, theme toggle, GA4, PWA install, autoFitText
  home.php             - Landing page (unauthenticated)
  onboarding.php       - Registration/onboarding
  register.php         - Registration form
  login.php            - Login page
  forgot_password.php  - Forgot password (email OTP)
  reset_password.php   - Reset password form
  dashboard.php        - Dashboard landing (store list + stats)
  dashboard_overview.php - Per-store dashboard (7-day Chart.js chart)
  dashboard_products.php - Product management (CRUD UI)
  dashboard_settings.php - Store settings (branding, contact info, social media)
  dashboard_tokens.php - Token purchase/transfer/withdraw with PIN modal
  create_store.php     - Create additional store
  marketplace.php      - Public marketplace + stores list
  orders.php           - Vendor order tracking with date filter + CSV
  profile.php          - Edit user name/email/password + transaction PIN management
  storefront.php       - Public storefront (hero, products, footer with social/contact)
  product_detail.php   - Single product detail page
  bill_payment.php     - Bill payment (airtime, data, electricity, cable, betting, epins)
  game.php             - Gamepad landing page (GPTokens balance, game cards, exchange button)
  game_color_swipe.php - Color Swipe match-3 game
  game_space_shooter.php - Space Shooter game
  game_exchange.php    - GPToken to VompCoin exchange page
  tokens.php           - Standalone token purchase/transfer/withdraw page (no store required)
  stores.php           - Browse all storefronts
  tokens.php           - Token management (buy/transfer/withdraw) at user level
admin/
  dashboard.php        - Admin dashboard (stats + commission)
  users.php            - User management
  stores.php           - Store management (visits + orders columns)
  products.php         - Product management
  orders.php           - Finance: withdrawals + token transactions + bill payments + commission
api/
  register.php         - POST /api/register
  login.php            - POST /api/login
  logout.php           - POST /api/logout
  profile.php          - POST /api/profile
  set_pin.php          - POST /api/set_pin
  verify_pin.php       - POST /api/verify_pin
  products.php         - POST /api/products (create/delete)
  settings.php         - POST /api/settings
  store_create.php     - POST /api/store_create.php
  tokens_purchase.php  - POST /api/tokens/purchase
  tokens_verify.php    - GET /api/tokens_verify.php (Paystack callback)
  tokens_transfer.php  - POST /api/tokens/transfer
  tokens_withdraw.php  - POST /api/tokens/withdraw
  tokens_deduct.php    - POST /api/tokens/deduct
  game_submit_score.php - POST /api/game_submit_score.php
  game_exchange.php    - POST /api/game_exchange.php
  track_affiliate_click.php - POST /api/track_affiliate_click.php (records affiliate click as an order with "Affiliate Visitor" as customer)
  bill_payment.php     - POST /api/bill_payment.php (airtime, data, electricity, tv, betting, epins, verify)
  bill_variations.php  - GET /api/bill_variations.php
  list_banks.php       - GET /api/list_banks.php
  resolve_account.php  - POST /api/resolve_account.php
  save_bank_details.php - POST /api/save_bank_details.php
  orders_export.php    - GET /api/orders_export.php
  admin_export.php     - GET /api/admin/export?type=users|stores|products|transactions|withdrawals|bill_payments
  admin_backfill_bill_commission.php - One-time script to backfill commission on existing bill_payments (admin only)
  admin/
    reset_password.php - POST /api/admin/reset_password (admin resets any user's password)
uploads/               - Uploaded product images
assets/
  theme.css            - Dark/light mode CSS overrides
  img/                 - Logo, icons, screenshots
cache/
  .gitkeep             - VTU.NG JWT token cache (vtung_token.json)
logs/
  app.log              - Rotated manually
```

## Routing
All requests go through `index.php`. `.htaccess` serves static assets directly and skips rewrite for existing .php files (API files served directly, not through index.php). `router.php` is for the PHP built-in server.

Routes in `index.php`:
- `GET /` → home page
- `GET /login`, `/register`, `/onboarding`, `/forgot-password`, `/reset-password` → auth pages
- `GET /dashboard` → store list + stats
- `GET /dashboard/create-store` → create additional store
- `GET /dashboard/{slug}` → store overview
- `GET /dashboard/{slug}/products|settings|tokens` → sub-pages
- `GET /marketplace` → browse products/stores (`?q=` search, `?category=` filter, `?country=` filter)
- `GET /products` → browse all products (`?q=` search, `?category=` filter, `?country=` filter, paginated)
- `GET /stores` → browse all storefronts
- `GET /orders` → vendor order tracking with date filter
- `GET /profile` → edit user name/email/password/PIN
- `GET /tokens` → token purchase/transfer/withdraw (user-level, no store required)
- `GET /bill-payment` → bill payment services
- `GET /game` → gamepad landing page
- `GET /game/color-swipe`, `/game/space-shooter` → games
- `GET /game/exchange` → GPToken to VompCoin exchange
- `GET /store/{slug}` → public storefront
- `GET /store/{slug}/{productId}` → product detail
- `GET /admin` → admin dashboard
- `GET /admin/users|stores|products|orders` → admin management pages
- `GET /admin/backfill-bill-commission` → one-time bill commission backfill
- `POST /api/*` → API handlers (all API files are self-contained with require_once chains)

Auth guards: Dashboard/*, orders, profile, tokens, bill-payment, game/exchange require authentication (redirect to /login). Login/register redirect authenticated users to /dashboard. Admin/* guarded by `auth_is_admin()`.

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
The `index.php` router sets variables (e.g. `$store`, `$products`, `$transactions`, `$currentUser`), includes the template, then includes `layout.php` which echoes `$content`. `$currentUser` is always available (null if not logged in).

## Database Schema (auto-created in `db_init_schema`, migrations via `db_ensure_column`)

### Users
- `id` VARCHAR(24) PK, `name`, `email`, `phone`, `password`, `token_balance` INT DEFAULT 0, `plan` VARCHAR(20) DEFAULT 'free', `role` VARCHAR(20) DEFAULT 'user', `gptokens` INT DEFAULT 0, `transaction_pin` VARCHAR(255), `bank_name`, `bank_account_number`, `bank_account_name`, `created_at`, `updated_at`

### Stores
- `id` VARCHAR(24) PK, `name`, `slug`, `description`, `owner_id`, `contact_phone`, `contact_email`, `logo_url`, `hero_image_url`, `hero_color`, `accent_color`, `token_balance` INT DEFAULT 50, `plan` VARCHAR(20) DEFAULT 'free', `is_active` TINYINT(1) DEFAULT 1, `social_facebook`, `social_instagram`, `social_twitter`, `social_tiktok`, `social_youtube`, `visits` INT DEFAULT 0, `created_at`, `updated_at`

### Products
- `id` VARCHAR(24) PK, `name`, `price` DECIMAL(10,2), `description`, `media_url` TEXT, `media_type` VARCHAR(20) DEFAULT 'image', `is_available` TINYINT(1) DEFAULT 1, `category` VARCHAR(100) DEFAULT 'Others', `product_condition` VARCHAR(50) DEFAULT 'Brand-New', `location`, `store_id` VARCHAR(24), `affiliate_url` TEXT, `created_at`, `updated_at`

### Orders
- `id` VARCHAR(24) PK, `store_id` VARCHAR(24), `product_id` VARCHAR(24), `product_name`, `product_price` DECIMAL(10,2), `customer_name`, `customer_phone`, `quantity` INT DEFAULT 1, `status` VARCHAR(20) DEFAULT 'pending', `created_at`

### Sessions
- `id` VARCHAR(24) PK, `user_id` VARCHAR(24), `token` VARCHAR(64), `expires_at` DATETIME, `created_at`

### Token Transactions
- `id` VARCHAR(24) PK, `user_id` VARCHAR(24), `store_id` VARCHAR(24), `type` VARCHAR(20) (credit/debit), `amount` INT, `description`, `created_at`

### Store Visits
- `id` VARCHAR(24) PK, `store_id` VARCHAR(24), `ip_address` VARCHAR(45), `visited_at` DATETIME (unique per IP per day via application logic)

### Bill Payments
- `id` VARCHAR(24) PK, `user_id` VARCHAR(24), `type` VARCHAR(20) (airtime/data/electricity/tv/betting/epins), `service_id` VARCHAR(50), `customer_id` VARCHAR(100), `amount_naira` DECIMAL(10,2), `commission` DECIMAL(10,2) DEFAULT 0, `coins_deducted` INT, `provider_ref` VARCHAR(100), `status` VARCHAR(20) DEFAULT 'processing', `meta_data` TEXT, `created_at` DATETIME

### Withdrawals
- `id` VARCHAR(24) PK, `user_id` VARCHAR(24), `amount` INT (VC), `naira_amount` INT, `token_rate` INT DEFAULT 20, `bank_name`, `account_number`, `account_name`, `bank_code`, `recipient_code`, `transfer_code`, `status` VARCHAR(20) DEFAULT 'pending', `created_at`

### Password Resets
- `id` VARCHAR(24) PK, `user_id` VARCHAR(24), `otp` VARCHAR(6), `expires_at` DATETIME, `created_at`

### Product Categories
- `id` VARCHAR(24) PK, `name` VARCHAR(100), `sort_order` INT

IDs are generated with `bin2hex(random_bytes(12))` (24-char hex).

## API Endpoints (POST)
| Endpoint | Auth | Action |
|---|---|---|
| `/api/register` | No | Register user (pure account creation, no store) |
| `/api/login` | No | Authenticate & set session cookie |
| `/api/logout` | Yes | Destroy session |
| `/api/profile` | Yes | Update name/email/password (requires current_password for password change) |
| `/api/set_pin` | Yes | Set or change 4-digit transaction PIN |
| `/api/verify_pin` | Yes | Verify PIN, returns `setup_required` if no PIN set |
| `/api/products?storeSlug=X&action=create` | Yes | Create product (multipart, file upload) |
| `/api/products?storeSlug=X&action=delete&id=Y` | Yes | Delete product (also deletes image file) |
| `/api/settings?storeSlug=X` | Yes | Update store settings (branding, contact, social media) |
| `/api/store_create.php` | Yes | Create additional store |
| `/api/tokens/purchase` | Yes | Initialize Paystack checkout for token purchase (requires pin) |
| `/api/tokens/transfer` | Yes | Transfer tokens to another user by email (requires pin) |
| `/api/tokens/withdraw` | Yes | Withdraw tokens to bank via Paystack Transfer (requires pin) |
| `/api/tokens/deduct` | Yes | Generate WhatsApp order URL (free, no token deduction) |
| `/api/game_submit_score.php` | Yes | Submit game score (adds to users.gptokens) |
| `/api/game_exchange.php` | Yes | Exchange 1M GPTokens for 50 Vomp Coins (atomic) |
| `/api/track_affiliate_click.php` | No | Record an affiliate click as an order ("Affiliate Visitor" as customer) |
| `/api/bill_payment.php` | Yes | Pay bill (airtime/data/electricity/tv/betting/epins, requires pin) |
| `/api/resolve_account.php` | Yes | Resolve bank account number |
| `/api/save_bank_details.php` | Yes | Save bank details on user profile |
| `/api/admin/reset_password` | Admin | Reset any user's password (admin only) |

## Key Backend Functions

### Database.php
- `db_get_connection()` — PDO singleton (MySQL), calls `db_init_schema()` on first connection
- `db_init_schema()` — Creates all tables + runs migrations (db_ensure_column calls)
- `db_ensure_column($db, $table, $column, $definition)` — Adds column if not exists
- `db_query($sql, $params)` — Prepare + execute, return statement
- `db_insert($table, $data)` — Insert associative array
- `db_update($table, $data, $where)` — Update with WHERE clause
- `db_fetch_all($sql, $params)` — Fetch all rows
- `img_url($path)` — Returns image URL with / prefix, or placeholder if empty

### Auth.php
- `auth_register($name, $email, $password, $phone)` — Pure account creation (no store)
- `auth_login($email, $password)` — Authenticate, set session cookie
- `auth_logout()` — Destroy session
- `auth_get_current_user()` — Returns user row or null
- `auth_update_user($userId, $data)` — Update name/email/password
- `auth_verify_transaction_pin($userId, $pin)` — Bcrypt verify PIN, returns `setup_required` if none set
- `auth_is_admin()` — Check if current user has role 'admin'

### Store.php
- `store_get_all_active()`, `store_get_by_slug($slug)`, `store_get_with_products($slug)`
- `store_get_by_slug_for_owner($slug, $ownerId)`, `store_get_user_stores($userId)`
- `store_create_for_user($ownerId, $name, $description, $contactPhone, $contactEmail)` — 50 free tokens
- `store_update($storeId, $data)`, `store_toggle_active($storeId)`
- `store_track_visit($storeId, $ip)` — Unique per IP per day
- `store_get_visits_last_7_days($storeId)` — For dashboard chart

### Product.php
- `product_get_all_available()`, `product_get_by_category($category)`
- `product_get_categories()`, `product_search($query)`
- `product_get_products_by_store($storeId)`, `product_get_available_products_by_store($storeId)`
- Product count sort for marketplace/stores listing

### Token.php
- `token_user_balance($userId)` — Get user-level token balance
- `token_purchase($storeId, $tokenCount)` — Add tokens, upgrade plan to 'premium'
- `token_purchase_user($userId, $tokenCount)` — User-level token purchase
- `token_transfer($fromUserId, $toUserId, $amount)` — Transfer between users
- `token_withdraw($userId, $amount, $nairaAmount, $bankDetails)` — Initiate withdrawal
- `token_deduct_for_product_upload($storeId)` — Deduct 10 tokens
- `token_history($storeId, $limit)`, `token_history_by_date()`
- `token_user_history($userId, $limit)` — User-level transaction history
- Constants: `TOKEN_PRICE_PER_UNIT` (20), `TOKEN_MINIMUM` (50)

### Paystack.php
- `paystack_load_config()`, `paystack_secret_key()`, `paystack_public_key()`
- `paystack_initialize($email, $amountKobo, $metadata, $callbackUrl)` — Create checkout
- `paystack_verify($reference)` — Verify transaction
- `paystack_list_banks()` — Fetch bank list (no caching)
- `paystack_resolve_account($accountNumber, $bankCode)` — Resolve account
- `paystack_create_transfer_recipient($name, $accountNumber, $bankCode)`
- `paystack_initiate_transfer($amountKobo, $recipientCode, $reason)`
- All use `file_get_contents` with stream context (no cURL)

### BillPayment.php
- `vtung_get_token()` — Get/cache JWT in `cache/vtung_token.json` (6-day expiry)
- `vtung_http_post($endpoint, $data)` — Authenticated POST to VTU.NG API v2
- `vtung_purchase_airtime()`, `vtung_purchase_data()`, `vtung_purchase_electricity()`, `vtung_purchase_tv()`, `vtung_purchase_betting()`, `vtung_purchase_epins()`
- `vtung_get_data_variations($service_id)` — Public GET, no auth (fetches data plans)
- `vtung_get_tv_variations($service_id)` — Public GET, no auth (fetches TV plans)
- `vtung_verify_customer($customerId, $serviceId, $variationId)`
- `bill_deduct_coins($userId, $coinsToDeduct, $type, $serviceId, $customerId, $amountNaira, ...)` — Deduct VC + log bill_payments record with commission
- `bill_naira_to_coins($amountNaira)` — `ceil(amount / TOKEN_PRICE_PER_UNIT)`
- Constant: `BILL_COMMISSION_PERCENT` (5) — Platform commission rate on bill payments

### Order.php
- `order_create($storeId, $productId, $customerName, $customerPhone, $quantity)`
- `order_get_orders_by_store($storeId, $from, $to, $statusFilter)`
- `order_get_all_for_user($userId)` — All orders across user's stores
- `order_count_by_store($storeId)`, `order_count_today_by_store($storeId)`

### Mailer.php
- `mailer_send($to, $subject, $html)` — Generic Resend API wrapper
- `mailer_send_otp($email, $otp)` — Password reset OTP
- `mailer_notify_withdrawal($userName, $userEmail, $amount, $nairaAmount, $bankName, $accountNumber, $accountName, $status, $error)` — Notify admin on withdrawal
- `mailer_notify_bill_payment($userName, $userEmail, $type, $serviceId, $customerId, $amount, $error)` — Notify admin on bill payment failure

### Admin.php
- `admin_commission_summary()` — Returns total_withdrawals, total_naira_withdrawn, total_commission (2% of withdrawals), total_bill_payments, total_bill_commission (sum of bill_payments.commission)
- Paginated queries: `admin_get_users_paginated`, `admin_get_stores_paginated`, `admin_get_products_paginated`, `admin_get_transactions_paginated`, `admin_get_withdrawals_paginated`, `admin_get_bill_payments_paginated`
- Search variants for each + count functions

## Affiliate Products
- Products can be sourced from affiliate sites instead of the seller's own inventory
- Affiliate products have an `affiliate_url` column in the DB (TEXT, NULL for own products)
- **Creation:** Dashboard product form has two tabs: "Add From My Store" (file upload) and "Add From Affiliate Site" (image URL + affiliate URL)
- **Token deduction:** Affiliate products do NOT deduct 10 Vomp Coins (only own products do)
- **Display:** Affiliate badge (purple "Affiliate" tag) appears on product cards in storefront, marketplace, dashboard, and all products page
- **Product detail:** Shows "Buy on Affiliate Site" button (external link) instead of "Order via WhatsApp"
- **Tracking:** Clicking an affiliate link triggers `api/track_affiliate_click.php` via JS (`trackAffiliateClick()` function), which records an order with customer name "Affiliate Visitor"
- **Affiliate URL:** Seller provides the external purchase URL; buyers are redirected in a new tab on click

## Token System
- 1 Vomp Coin (VC) = ₦20
- Token balance stored on both `users.token_balance` (user-level) and per-store
- **Minimum purchase:** 50 tokens for ₦1,000
- **Minimum withdrawal:** 5 VC (₦100). Fee: 2% Paystack + 2% platform
- **Purchase flow:** User enters amount → Paystack checkout → callback verifies → tokens credited
- **Transfer:** By email between users
- **Withdrawal:** Paystack Transfer API (automatic). Errors hidden behind "network unstable"
- **Bill payment:** VC deducted per service cost (ceil(naira/20))

## Game System
- Two games: Color Swipe (match-3) and Space Shooter (Canvas API)
- Scores submitted via `api/game_submit_score.php` — cumulative addition to `users.gptokens`
- Difficulty progression: 8 levels (Color Swipe), 5 waves by play time (Space Shooter)
- Sound via Web Audio API (no audio files)
- Pages hidden from nav drawer

## GPToken Exchange
- **Threshold:** 1,000,000 GPTokens minimum
- **Rate:** 1M GPT = 50 Vomp Coins (20,000 GPT = 1 VC)
- **No partial exchanges** — button only enables at threshold
- **Atomic transaction:** `api/game_exchange.php` deducts GPT, credits VC, logs transaction in DB transaction
- Exchange rate card HTML-commented out (hidden from users)
- Exchange page at `/game/exchange`

## Transaction PIN
- 4-digit PIN, bcrypt-hashed, stored in `users.transaction_pin`
- Required for: token purchase, transfer, withdrawal, bill payment
- Auto-prompt: if no PIN set, `api/verify_pin.php` returns `setup_required: true`
- PIN modal flow: `requirePin(action, payload)` → `openPinModal()` → user enters PIN → `verify_pin.php` → on success `closePinModal()` + `executePending(pin)` reads pending vars
- **Critical:** `closePinModal()` must NOT clear pending vars (`pendingAction`/`pendingPayload`/`PENDING_PAYLOAD`). Only the execution function clears after reading.

## Bill Payment Commission
- Every bill payment records `commission = round(amount_naira * 5 / 100, 2)` in the `bill_payments` table
- Admin finance page (`/admin/orders`) shows a "Bill Payment Commission (5%)" card with total ₦ and payment count
- Bill payments section with table (User, Type, Service, Customer, Amount, Commission, Status, Date)
- CSV export for bill payments at `/api/admin/export?type=bill_payments`
- One-time backfill script for existing records: `/admin/backfill-bill-commission`
- Commission is calculated at deduction time and stored per-transaction

## Admin Panel
- Routes under `/admin/*`, guarded by `auth_is_admin()`
- Admin dashboard: total users, stores, products, transactions, commission earned
- Users: list, search, CSV export, admin can reset any user's password via modal
- Stores: list with visits + orders columns, toggle active/inactive, CSV export
- Products: list, search, CSV export
- Orders (finance): withdrawals + token transactions + bill payments + commission summary cards, CSV export
- Phone numbers are clickable WhatsApp links in all tables
- All list pages support CSV export

## WhatsApp Orders
- Orders are **free** — no Vomp Coin deduction per order
- `order_create()` in `backend/Order.php` generates order record with customer name/phone
- Order tracking with date filtering + CSV export on `/orders` page

## Dark/Light Mode
- Persisted via localStorage, toggle saves preference then `location.reload()`
- FOUC prevention via inline `<head>` script
- CSS overrides in `assets/theme.css` with `[data-theme="light"]` selectors

## PWA
- `manifest.json` with `purpose: "any maskable"`, `display_override`, `screenshots`
- `sw.js` — service worker
- Download buttons trigger native PWA install prompt on desktop/Android
- Icon lookup: falls back to next available size

## Conventions
- All PHP functions are in the global namespace (no classes)
- Database queries use prepared statements via PDO
- User-facing strings use `htmlspecialchars()` for XSS prevention
- File uploads stored in `uploads/` with GD compression + `uniqid` prefix
- UI uses `glass-morphism` CSS class + Tailwind CDN
- Currency is Nigerian Naira (₦)
- Responsive: mobile-first with `md:` breakpoints
- Accent/button color: `#ff610a` (orange)
- All HTTP via `file_get_contents` with stream context (no cURL)
- TomSelect initialized on visible elements, destroyed/re-created on dynamic option changes
- Input type="date" on iOS: `min-w-0 max-w-full overflow-hidden`
- Error hiding: withdrawal and bill payment errors show "network unstable" to users; real errors emailed to `virtualopenmarket@gmail.com`

## .env Config
```
DB_HOST=localhost
DB_NAME=vomp
DB_USER=root
DB_PASS=
DB_PORT=3306

PAYSTACK_SECRET_KEY=sk_test_xxx
PAYSTACK_PUBLIC_KEY=pk_test_xxx
APP_URL=http://localhost:8000

VTU_NG_USERNAME=your_vtung_email
VTU_NG_PASSWORD=your_vtung_password

RESEND_API_KEY=re_xxx
RESEND_FROM=vomp <noreply@domain.com>
```

## Dev Server
```bash
php -S localhost:8000 router.php
```
