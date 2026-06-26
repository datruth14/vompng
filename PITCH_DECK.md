# VomP — Pitch Deck

**Tagline:** Africa's Simplest Marketplace + Play-to-Earn Platform

---

## 1. Elevator Pitch

VomP is a mobile-first marketplace platform where anyone in Africa can create a digital storefront in under 60 seconds, list products, and receive orders directly via WhatsApp — no technical skills, no app download, no merchant account required. On top of the marketplace, VomP layers a token economy (Vomp Wallet) and a play-to-earn GamePad hub where users earn GPTokens that convert to spendable coins.

---

## 2. Problem

**Why selling online is still hard in Africa:**

- **Platform gatekeeping:** Jumia, Konga, and similar platforms control the customer relationship, take high commissions, and require logistics partnerships.
- **Technical barriers:** Setting up a Shopify or WooCommerce store requires hosting, domain, payment gateway setup, and technical know-how.
- **Payment friction:** Credit card penetration in Nigeria is below 5%. Cash-on-delivery has high failure rates. Mobile money and bank transfers are fragmented.
- **Social commerce is manual:** Most sellers use Instagram/WhatsApp manually — posting product photos, taking orders in DMs, tracking in notebooks. No automation, no analytics.
- **No monetization for users:** Users spend time and attention on platforms but earn nothing. VomP flips this with play-to-earn gaming.

---

## 3. Solution

**VomP — WhatsApp-native marketplace with a token economy and gaming layer.**

- **Instant store creation:** Name, email, password — done. A store with 50 free tokens is created.
- **WhatsApp order flow:** Buyer clicks "Order via WhatsApp" → pre-filled message with product details sent to seller. No checkout friction. No payment failure.
- **Vomp Wallet (token economy):** Users buy, earn, and spend Vomp Coins (1 VC = ₦20). Coins are used for bill payments (airtime, data, electricity, TV, betting), product listing fees, and future platform services.
- **GamePad play-to-earn:** Users play Color Swipe and Space Shooter to earn GPTokens, which can be exchanged for Vomp Coins (1M GPT = 50 VC).
- **Affiliate products:** Sellers can list products from external affiliate sites without holding inventory.
- **Multi-store support:** One user, multiple stores — each with its own branding, hero image, accent color, and social links.
- **Bill payment services:** Users pay for airtime, data, electricity, cable TV, and betting subscriptions using Vomp Coins — powered by VTU.NG API.

---

## 4. Market Opportunity

| Metric | Value |
|---|---|
| Nigeria e-commerce market size (2024) | ~$13B |
| Nigerian smartphone users | ~110M+ |
| WhatsApp users in Nigeria | ~100M+ |
| Active merchants on traditional platforms | <100K |
| Informal merchants selling via social media | 10M+ |

**Target:** The 10M+ informal merchants and content creators who sell via Instagram, Facebook, and WhatsApp but lack a proper storefront and payment infrastructure.

**Adjacent market:** Gamers and casual users who want to earn real value from play time. Nigeria's gaming market is projected at $200M+.

---

## 5. Product Features

### Marketplace
- Public marketplace with product grid, categories, country filter, and search
- Product detail pages with WhatsApp order modal
- Storefront with custom branding (hero image/color, accent color, social media links)
- Explore Stores page sorted by product count
- Responsive mobile-first design with dark/light mode
- PWA — installable on Android, iOS, and Desktop

### Store Dashboard
- Overview with stats (balance, orders, products, visits, 7-day chart)
- Product management (CRUD with image upload + client-side compression)
- Store settings (branding, contact info, social media handles)
- Orders page with date filtering, pagination, CSV export
- Token purchase, transfer, and withdrawal via Paystack

### Token Economy (Vomp Wallet)
- User-level Vomp Coin balance (1 VC = ₦20)
- Purchase via Paystack (min 50 VC = ₦1,000)
- Transfer coins to other users by email
- Withdraw coins to bank via Paystack Transfer API (min 5 VC)
- Transaction history with date filtering
- Bill payment (airtime, data, electricity, TV, betting, ePINs)

### GamePad — Play-to-Earn
- **Color Swipe** — match-3 puzzle with 8 progressive difficulty levels
- **Space Shooter** — drag-to-move arcade shooter with progressive waves
- GPTokens earned per game session, cumulative on user account
- Exchange 1M GPTokens for 50 Vomp Coins
- All games use Canvas API (no dependencies), Web Audio API for sounds

### Admin Panel
- Users, Stores, Products, Transactions, Withdrawals — all with search, pagination, CSV export
- Admin can reset any user's password
- Store analytics: unique daily visits + order count per store
- Store toggle (enable/disable)
- Admin commission summary (withdrawals + platform fees + bill payment commission)
- WhatsApp clickable phone links

---

## 6. Business Model

### Revenue Streams

| Stream | Description | Margin |
|---|---|---|
| **Token sales** | Users buy Vomp Coins at ₦20 each. Cost of goods is ₦0 (digital). Paystack fee ~1.5–3%. | ~97% |
| **Withdrawal fee** | 2% platform fee + 2% Paystack fee on cash-outs | 2% |
| **Bill payment commission** | 5% commission on airtime, data, electricity, TV, betting | 5% |
| **Product listing fee** | 10 Vomp Coins deducted per own-product upload | ~₦200 per listing |
| **Affiliate tracking** | Future: commission on affiliate product sales | Variable |
| **Premium stores** | Future: subscription tiers for advanced analytics, featured listings | TBD |

### Unit Economics

- **User acquisition cost:** ₦0 (organic/word-of-mouth)
- **Average token purchase:** ₦1,000 (50 VC)
- **Lifetime value:** ₦2,500+ (repeat purchases, bill payments, gaming)

---

## 7. Traction & Milestones

### Built
- Complete marketplace platform with 40+ PHP files
- Token economy with Paystack integration (purchase, transfer, withdrawal)
- Bill payment service via VTU.NG API (airtime, data, electricity, TV, betting, ePINs)
- Two play-to-earn games (Color Swipe, Space Shooter)
- Admin panel with full CRUD, analytics, commission tracking
- PWA with install prompt, dark/light mode
- Affiliate product system
- Multi-store support
- Country/currency/location filters

### Next Milestones
1. Live user onboarding & beta testing
2. Marketing push via WhatsApp/social media
3. Premium store subscriptions
4. Mobile app (Flutter bridge)
5. Affiliate marketplace for product sourcing
6. Escrow payment system between buyers and sellers

---

## 8. Technology

| Layer | Stack |
|---|---|
| **Backend** | Vanilla PHP 8+, MySQL (PDO) |
| **Frontend** | PHP templates, Tailwind CSS (CDN), vanilla JS |
| **Payments** | Paystack (checkout + Transfer API) |
| **Bill Payments** | VTU.NG API v2 (JWT auth) |
| **Email** | Resend API |
| **Auth** | Cookie-based sessions (SHA-256, bcrypt) |
| **Image Processing** | GD library + client-side compression (WebP, max 1600px) |
| **Games** | Canvas API, Web Audio API |
| **PWA** | Manifest + Service Worker |
| **Hosting** | Any PHP 8+ shared/VPS (no framework, no Composer) |

**Key architectural decisions:**
- Zero external PHP dependencies — no Composer, no vendor directory
- No cURL — all HTTP via `file_get_contents` with stream context
- Mobile-first, responsive, glass-morphism UI

---

## 9. Team

**Founder / Lead Developer:** [Name]

Building VomP as a solo founder with full-stack capability across PHP, MySQL, JavaScript, Canvas API, Paystack API, VTU.NG API, and PWA.

---

## 10. Ask

We are seeking:
- **Beta users** — Sellers and gamers to test and provide feedback
- **Strategic partners** — VTU.NG reseller upgrades, affiliate product networks
- **Investment** — $20K seed for marketing, hosting scaling, and mobile app development

---

*Built with PHP, MySQL, and a lot of coffee. No VCs were harmed in the making of this platform.*
