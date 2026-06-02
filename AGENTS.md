# VomP — Agent Guide

## Overview
VomP is a PHP-based marketplace platform where sellers create stores, list products, and receive orders via WhatsApp. Token system (purchase/transfer/withdraw), super admin dashboard, PWA support.

## Tech Stack
- **Backend:** Vanilla PHP (no framework), MySQL via PDO
- **Frontend:** PHP template files, Tailwind CSS (CDN), vanilla JS
- **Database:** MySQL (InnoDB) via PDO
- **Auth:** Cookie-based sessions (SHA-256 hashed tokens, bcrypt passwords)
- **Email:** Resend API (raw HTTP)
- **Paystack:** Payment, bank list, bank resolution, transfers
- **PWA:** Manifest + service worker

## Key Architecture Decisions
- Registration is pure account creation — no store is created during registration. Store creation is optional from dashboard only.
- WhatsApp orders are free — no Vomp Coin deduction per order.
- All withdrawal errors return "network unstable" (hides Paystack wallet funding issues from users). Transfer errors show real balance.
- cURL NOT available — all HTTP via `file_get_contents` with stream context.
- Number inputs use `type="text"` with `inputmode="numeric"` for comma formatting.
- `img_url()` helper normalizes legacy image URLs (adds leading `/` if missing).
- Product queries handle legacy data: `store_id` may equal `owner_id` for migrated products.

## Progress Summary

### Done
- **Database:** SQLite → MySQL migration (PDO, InnoDB, NOW(), `LIMIT ? OFFSET ?`). Schema auto-creates on first connect.
- **Image upload:** GD compression (JPEG/PNG/WebP), client-side compression via XHR, progress bar. `product_delete()` removes image from filesystem.
- **Forgot password:** Resend OTP via email, password_resets table, routes at `/forgot-password` and `/reset-password`.
- **Auth:** `auth_register()` takes only name/email/password/phone. No store creation. Seeds 50 tokens to user.
- **Store creation:** Only via `/dashboard/create-store` → `api/store_create.php`. Auto-fills user phone/email.
- **Dashboard empty state:** Card with "Create Your First Store" button when user has 0 stores.
- **Tokens system:** User-level token balance. Purchase/transfer/withdrawal without requiring a store. Standalone `/tokens` route.
  - **Purchase:** Paystack initialize/verify. Works with or without `storeSlug`.
  - **Transfer:** By email recipient. Transaction history shows sender name/email.
  - **Withdrawal:** Paystack Transfer API. 2% Paystack + 2% platform fee. Minimum 5 coins (₦100). Bank details saved on user after first withdrawal. Generic "network unstable" error for failures. Email notification to virtualopenmarket@gmail.com.
  - **Bank list:** Live from Paystack (no cache, 8s timeout, max 3 pages). Tom Select lazy-initializes on Withdraw tab open.
- **Orders system:** `orders` table, vendor order tracking with pagination, date filtering, CSV export.
- **Super admin dashboard:** `/admin/*` routes, `auth_is_admin()`, store toggle, CSV export (users/stores/products/transactions/withdrawals), WhatsApp phone links. Stores sorted by product count desc. Admin finance page with commission summary.
- **Marketplace & Stores pages:** Sorted by product count descending.
- **PWA:** manifest.json (standalone, orange theme), sw.js (network-first for pages, cache-first for assets, skips API POSTs/admin). Service worker registered in layout.php. `beforeinstallprompt` captured globally.
- **Download page:** Three glassmorphism cards (Android/iOS/Desktop). Desktop/Android trigger native install prompt. iOS shows Safari instructions modal.
- **Social icons:** YouTube, Facebook, Instagram in marketplace/stores/products footer. Share FAB on store pages.
- **UI fixes:** iOS safe area (`env(safe-area-inset-bottom)`), text-fit auto-shrink, formatted number inputs.

### In Progress
- (none)

### Blocked
- (none)

## Next Steps
1. Pull latest code on cPanel (`git pull origin main`).

## Database Tables
- Users, Stores, Products, Sessions, Token Transactions, Orders, Password Resets

## Routing
All through `index.php`. Static files bypass via `router.php` / `.htaccess`. API `.php` files served directly.

## Token System
- Price: ₦20 per Vomp Coin (minimum purchase: 50 coins)
- Transfer: by email address
- Withdrawal: Paystack Transfer API, min 5 coins, 2%+2% fee
- Orders: free (no coin deduction)

## Dev Server
```bash
php -S localhost:8000 router.php
```
