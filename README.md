# VomP — Marketplace + Gamepad Platform

A PHP-based marketplace platform where sellers create stores, list products, and receive orders via WhatsApp. Features a token economy, admin panel, and a Gamepad gaming hub where users earn GPTokens.

## Tech Stack

- **Backend:** Vanilla PHP (no framework), MySQL via PDO
- **Frontend:** PHP template files, Tailwind CSS (CDN), vanilla JS, Chart.js (CDN)
- **Database:** MySQL with InnoDB
- **Auth:** Cookie-based sessions (SHA-256 hashed tokens, bcrypt passwords)
- **Payments:** Paystack (token purchase, withdrawals via Transfer API)
- **Emails:** Resend API (transactional emails)
- **Animations:** Animate.css (entrance effects)
- **PWA:** Manifest + Service Worker (offline-capable installable app)

## Features

### Marketplace
- Public marketplace with product grid, categories, search, storefronts
- Product detail page with WhatsApp order modal
- Storefront with custom branding (hero image/color, accent color, social media links)
- Explore Stores page sorted by product count
- Responsive design (mobile-first)

### Store Dashboard
- Store overview with stats (balance, orders, products, visits, 7-day chart)
- Product management (CRUD with image upload + client-side compression)
- Store settings (branding, contact info, social media handles)
- Orders page with date filtering, pagination, CSV export
- Token purchase, transfer, and withdrawal via Paystack

### Token Economy
- Vomp Coins — user-level balance (not per-store)
- Purchase via Paystack (₦20/coin, min 50 coins = ₦1,000)
- Transfer coins to other users by email
- Withdraw coins via Paystack Transfer API (min 5 coins, 2% + 2% fees)
- Transaction history with date filtering
- Admin commission tracking

### Gamepad — Play-to-Earn
- Landing page with GPTokens balance, game selection, random play
- **Color Swipe** — match-3 puzzle with 8 progressive difficulty levels, chain reactions, effects
- **Space Shooter** — drag-to-move arcade shooter with 5-wave progressive difficulty, color-coded enemies
- GPTokens earned per game session, cumulative on user account
- All games use Canvas API (no dependencies), Web Audio API for sounds

### Admin Panel
- Users, Stores, Products, Transactions, Withdrawals — all with search, pagination, CSV export
- Store analytics: unique daily visits + order count per store
- Store toggle (enable/disable)
- Admin commission summary (withdrawals + platform fees)
- Admin finance page with combined withdrawal + transaction view
- WhatsApp clickable phone links

### Analytics (Store Owners)
- Store Visits stat on dashboard (all-time + today)
- 7-day bar chart showing daily unique visitors + orders
- Visit tracking via IP + date dedup (no inflation on refresh)

### Authentication & User Features
- Register, login, logout (bcrypt passwords)
- Forgot password flow (OTP via email via Resend API)
- Profile editing (name, email, phone, password with current password verification)
- User-level token balance accessible without a store

### PWA
- Installable web app (manifest.json + service worker)
- Beforeinstallprompt for Android/Desktop
- iOS install instructions modal
- Network-first for pages, cache-first for static assets

### Dark / Light Mode
- Toggle in nav drawer, persisted to localStorage
- Comprehensive CSS variable overrides via `[data-theme="light"]`
- FOUC prevention (inline script in `<head>`)
- Meta theme-color switches with mode

## Directory Structure

```
index.php          — Main router (GET/POST routing + auth guards)
router.php         — PHP built-in server router (static files bypass)
.htaccess          — Apache rewrite rules (skips existing .php files)
config/database.php — MySQL connection config
.env               — API keys (Paystack, Resend) + APP_URL

backend/
  Database.php     — PDO singleton, schema init, generic query helpers
  Auth.php         — Register, login, logout, get_current_user, update_user
  Store.php        — Store CRUD + lookup by slug/owner + search + multi-store
  Product.php      — Product CRUD + search + category filter + image delete
  Token.php        — Token plans, purchase, deduct, transfer, withdraw, history
  Order.php        — Order create, get, paginated, count
  Admin.php        — Admin paginated queries, commission summary
  Paystack.php     — Paystack payment gateway (initialize, verify, transfer, bank list)
  Mailer.php       — Resend API wrapper (send, OTP, withdrawal notifications)
  Logger.php       — Simple file logger
  prune_sessions.php — CLI script to clean expired sessions (cron)

frontend/
  layout.php       — Main HTML shell with nav, mobile menu, flash messages, FAB drawer, theme toggle
  home.php         — Landing page (unauthenticated) with footer
  login.php        — Login page
  register.php     — Registration form (no store required)
  dashboard.php    — Dashboard landing (store list + stats)
  dashboard_overview.php — Per-store dashboard with stats + 7-day chart
  dashboard_products.php — Product management (CRUD UI)
  dashboard_settings.php — Store settings (branding, contact, social media)
  dashboard_tokens.php — Token purchase, transfer, withdrawal with Tom Select
  dashboard_orders.php — Orders table with date filter + CSV export
  create_store.php — Create additional store for existing user
  marketplace.php  — Product grid, categories, search, storefronts
  stores.php       — Explore Stores page sorted by product count
  products.php     — All products with horizontal scroll categories
  storefront.php   — Public storefront (hero, products, social footer, order modal)
  product_detail.php — Single product detail with order form modal
  orders.php       — Token activity with date filtering
  profile.php      — Edit name, email, password, phone
  game.php         — Gamepad landing page with GPTokens balance + game grid
  game_color_swipe.php — Color Swipe match-3 game (Canvas API)
  game_space_shooter.php — Space Shooter arcade game (Canvas API)
  admin/*.php      — Super admin templates

api/
  register.php     — POST /api/register
  login.php        — POST /api/login
  logout.php       — POST /api/logout
  products.php     — POST /api/products (create/delete)
  settings.php     — POST /api/settings (update store)
  tokens_purchase.php — POST /api/tokens/purchase (Paystack checkout)
  tokens_verify.php  — GET /api/tokens_verify.php (Paystack callback)
  tokens_transfer.php — POST /api/tokens_transfer
  tokens_withdraw.php — POST /api/tokens_withdraw
  tokens_deduct.php — POST /api/tokens_deduct (WhatsApp order, stores order)
  game_submit_score.php — POST /api/game_submit_score (auth required)
  profile.php      — POST /api/profile
  store_create.php  — POST /api/store_create
  admin/toggle-store.php — POST toggle store active status
  admin/export.php  — GET CSV export (users, stores, products, transactions, withdrawals)
  list_banks.php    — GET Paystack bank list (no cache)
  resolve_account.php — POST resolve bank account
  save_bank_details.php — POST save bank details to user
  orders_export.php  — GET CSV export per store orders
  forgot_password.php — POST send OTP
  reset_password.php — POST verify OTP + reset password
```

## Dev Server

```bash
php -S localhost:8000 router.php
```

## Environment Variables (`.env`)

```
PAYSTACK_SECRET_KEY=sk_test_...
PAYSTACK_PUBLIC_KEY=pk_test_...
APP_URL=http://localhost:8000
RESEND_API_KEY=re_...
RESEND_FROM=vomp <noreply@domain.com>
DB_HOST=localhost
DB_NAME=vomp
DB_USER=root
DB_PASS=
DB_PORT=3306
```

## Maintenance

Run the session prune script periodically to remove expired sessions:

```
php backend/prune_sessions.php
```

Example cron (hourly):

```
0 * * * * php /path/to/vomp/backend/prune_sessions.php >> /path/to/vomp/logs/prune.log 2>&1
```

## Key Architecture Notes

- **No Composer** — all PHP files committed directly. No vendor directory.
- **No cURL** — all HTTP uses `file_get_contents` with stream context (Paystack, Resend).
- **Legacy data** — old products have `store_id` set to `owner_id` instead of store ID. All queries handle this fallback.
- **GD required** — image upload compresses to JPEG 30 / PNG 9 / WebP 30.
- **Client-side compression** — images resized to max 1600px at 70% quality before upload.
- **Registration** creates only a user account (no store). Store creation is always optional from dashboard.
- **Super admin** — `14eter@gmail.com` auto-seeded as admin. Access at `/admin`.
- **Withdrawal errors** — all hidden behind "network unstable" to obscure Paystack wallet balance.
- **Token deduction** was removed from WhatsApp orders — orders are free.
