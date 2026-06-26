<?php
/*
 * Public store front-end template.
 */

// Track unique daily store visit
$visitorIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$today = date('Y-m-d');
$existing = db_fetch("SELECT id FROM store_visits WHERE store_id = ? AND ip_address = ? AND DATE(visited_at) = ?", [$store['id'], $visitorIp, $today]);
if (!$existing) {
    db_insert('store_visits', ['id' => bin2hex(random_bytes(12)), 'store_id' => $store['id'], 'ip_address' => $visitorIp]);
    db_get_connection()->prepare("UPDATE stores SET visits = visits + 1 WHERE id = ?")->execute([$store['id']]);
}

$pageTitle = htmlspecialchars($store['name']) . ' - Storefront';
ob_start();
?>
<section class="py-8 space-y-10 animate__animated animate__fadeInUp">
    <?php
    $heroUrl = $store['hero_image_url'] ?? '';
    if ($heroUrl && $heroUrl[0] !== '/') {
        $heroUrl = '/' . $heroUrl;
    }
    ?>
    <header class="glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 overflow-hidden relative min-h-[260px] flex items-center animate__animated animate__fadeInDown">
        <?php if (!empty($store['hero_image_url'])): ?>
            <div class="absolute inset-0 skeleton-box overflow-hidden">
                <img src="<?php echo htmlspecialchars($heroUrl); ?>" alt="" class="img-skeleton w-full h-full object-cover" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')" />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/70 to-transparent" style="z-index: 10;"></div>
        <?php else: ?>
            <div class="absolute inset-0 opacity-20" style="background: radial-gradient(circle at top right, <?php echo htmlspecialchars($store['accent_color'] ?? '#8b5cf6'); ?>, transparent 45%), radial-gradient(circle at bottom left, <?php echo htmlspecialchars($store['hero_color'] ?? '#4f46e5'); ?>, transparent 45%);"></div>
        <?php endif; ?>
        <div class="relative z-10 w-full">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff8c3a] mb-2">Storefront</p>
                    <h1 class="text-5xl font-black text-white tracking-tight mb-3"><?php echo htmlspecialchars($store['name']); ?></h1>
                    <p class="text-gray-300 max-w-2xl"><?php echo htmlspecialchars($store['description'] ?: 'Browse products and order directly via WhatsApp.'); ?></p>
                </div>

            </div>
        </div>
    </header>

    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-1">Latest Products</p>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight animate__animated animate__fadeInDown">New Arrivals</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($products as $product):
            $pImgUrl = $product['media_url'] ?? '';
            if ($pImgUrl && !str_starts_with($pImgUrl, 'http://') && !str_starts_with($pImgUrl, 'https://') && $pImgUrl[0] !== '/') {
                $pImgUrl = '/' . $pImgUrl;
            }
        ?>
            <article class="glass-morphism rounded-3xl p-5 border border-white/10 flex flex-col gap-4 animate__animated animate__fadeInUp">
                <div class="h-52 rounded-2xl overflow-hidden skeleton-box border border-white/5 relative">
                    <?php if (!empty($product['media_url'])): ?>
                        <img src="<?php echo htmlspecialchars($pImgUrl); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-skeleton w-full h-full object-cover" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-600">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 3h18M3 21h18M9 3v18" />
                            </svg>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($product['affiliate_url'])): ?>
                        <span class="absolute top-2 right-2 px-2 py-0.5 rounded-lg bg-purple-500/20 border border-purple-500/30 text-purple-300 text-[10px] font-black uppercase tracking-wider z-10">Affiliate</span>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-white"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="text-gray-400 text-sm mt-2 line-clamp-2"><?php echo htmlspecialchars($product['description'] ?: ''); ?></p>
                    <?php if (!empty($product['country'])): ?>
                        <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                            <span>📍 <?php echo htmlspecialchars($product['country']); ?><?php if (!empty($product['state'])): ?> &middot; <?php echo htmlspecialchars($product['state']); ?><?php endif; ?></span>
                            <span>💱 <?php echo htmlspecialchars(product_get_currency_symbol($product['currency'] ?? 'NGN')); ?> <?php echo htmlspecialchars($product['currency'] ?? 'NGN'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mt-auto flex flex-col gap-3">
                    <p class="text-[#ff8c3a] font-black text-xl"><?php echo htmlspecialchars(product_get_currency_symbol($product['currency'] ?? 'NGN')); ?><?php echo number_format((float) $product['price'], 2); ?></p>
                    <div class="flex flex-wrap gap-3">
                        <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>/<?php echo htmlspecialchars($product['id']); ?>" class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white font-black text-sm hover:bg-white/10 transition-all">View Details</a>
                        <?php if (!empty($product['affiliate_url'])): ?>
                            <a href="<?php echo htmlspecialchars($product['affiliate_url']); ?>" target="_blank" rel="noopener" onclick="trackAffiliateClick('<?php echo $product['id']; ?>', '<?php echo $store['slug']; ?>')" class="bg-[#ff610a] px-5 py-2.5 rounded-xl inline-flex items-center gap-2 text-white font-black text-sm">Buy on Affiliate Site <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg></a>
                        <?php else: ?>
                            <button class="bg-[#ff610a] px-5 py-2.5 rounded-xl order-btn" data-store="<?php echo htmlspecialchars($store['slug']); ?>" data-product="<?php echo htmlspecialchars($product['id']); ?>">Order via WhatsApp</button>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (!$products): ?>
        <div class="glass-morphism rounded-3xl p-10 border border-white/10 text-center">
            <h3 class="text-2xl font-black text-white mb-2">No products yet</h3>
            <p class="text-gray-400">This seller has not published any product right now.</p>
        </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-2 mt-10">
            <?php if ($page > 1): ?>
                <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>?page=<?php echo $page - 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>?page=<?php echo $i; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>?page=<?php echo $page + 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Store Footer -->
<footer class="glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 animate__animated animate__fadeInUp">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <h3 class="text-xl font-black text-white mb-2"><?php echo htmlspecialchars($store['name']); ?></h3>
            <p class="text-gray-400 text-sm leading-relaxed max-w-md"><?php echo htmlspecialchars($store['description'] ?: ''); ?></p>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-wider mb-4">Social Media</h4>
            <div class="flex flex-wrap gap-3">
                <?php if (!empty($store['social_facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($store['social_facebook']); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($store['social_instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($store['social_instagram']); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($store['social_tiktok'])): ?>
                    <a href="<?php echo htmlspecialchars($store['social_tiktok']); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($store['social_twitter'])): ?>
                    <a href="<?php echo htmlspecialchars($store['social_twitter']); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($store['social_youtube'])): ?>
                    <a href="<?php echo htmlspecialchars($store['social_youtube']); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-wider mb-4">Contact</h4>
            <ul class="space-y-3 text-sm text-gray-400">
                <?php if (!empty($store['contact_phone'])): ?>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        <a href="tel:<?php echo htmlspecialchars($store['contact_phone']); ?>" class="hover:text-white transition-colors"><?php echo htmlspecialchars($store['contact_phone']); ?></a>
                    </li>
                <?php endif; ?>
                <?php if (!empty($store['contact_email'])): ?>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <a href="mailto:<?php echo htmlspecialchars($store['contact_email']); ?>" class="hover:text-white transition-colors"><?php echo htmlspecialchars($store['contact_email']); ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="mt-8 pt-6 border-t border-white/5 text-center text-xs text-gray-600">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($store['name']); ?>. All rights reserved. Powered by <a href="/" class="text-[#ff610a] hover:underline">vomp</a>.
    </div>
</footer>

<!-- Order Form Modal -->
<div id="orderModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeOrderModal()"></div>
    <div class="relative glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <button onclick="closeOrderModal()" class="absolute top-4 right-4 p-2 rounded-xl bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <h2 class="text-2xl font-black text-white mb-2">Place Your Order</h2>
        <p class="text-gray-400 text-sm mb-6">Fill in your details below. Your order will be sent directly to the seller via WhatsApp.</p>
        <form id="orderForm" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Full Name</label>
                <input type="text" id="oName" required placeholder="e.g. John Doe" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Email Address</label>
                <input type="email" id="oEmail" required placeholder="you@example.com" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">State</label>
                <input type="text" id="oState" required placeholder="e.g. Lagos" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Delivery Location</label>
                <textarea id="oDelivery" required rows="2" placeholder="e.g. 123 Main Street, Ikeja" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all"></textarea>
            </div>
            <button type="submit" id="orderSubmit" class="btn-press w-full py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">
                Send Order via WhatsApp
            </button>
            <div id="orderFormMsg" class="mt-2"></div>
        </form>
    </div>
</div>

<script>
function trackAffiliateClick(productId, storeSlug) {
    fetch('/api/track_affiliate_click.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ product_id: productId, store_slug: storeSlug })
    }).catch(function(){});
}

let activeOrder = null;

function openOrderModal(btn) {
    activeOrder = {
        store: btn.dataset.store,
        product: btn.dataset.product || null,
    };
    document.getElementById('orderModal').classList.remove('hidden');
    document.documentElement.style.overflow = 'hidden';
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
    document.documentElement.style.overflow = '';
    activeOrder = null;
}

document.querySelectorAll('.order-btn').forEach((btn) => {
    btn.addEventListener('click', () => openOrderModal(btn));
});

document.getElementById('orderForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    if (!activeOrder) return;

    const btn = document.getElementById('orderSubmit');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    const payload = {
        name: document.getElementById('oName').value.trim(),
        email: document.getElementById('oEmail').value.trim(),
        state: document.getElementById('oState').value.trim(),
        delivery_location: document.getElementById('oDelivery').value.trim(),
    };

    let url = '/api/tokens_deduct.php?storeSlug=' + encodeURIComponent(activeOrder.store);
    if (activeOrder.product) {
        url += '&productId=' + encodeURIComponent(activeOrder.product);
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.whatsappUrl) {
            document.getElementById('orderFormMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">' + (data.error || 'Unable to generate WhatsApp link.') + '</div>';
            btn.disabled = false;
            btn.textContent = 'Send Order via WhatsApp';
            return;
        }
        window.location.href = data.whatsappUrl;
    } catch (err) {
        document.getElementById('orderFormMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Network error. Please try again.</div>';
        btn.disabled = false;
        btn.textContent = 'Send Order via WhatsApp';
    }
});
</script>
<?php
$content = ob_get_clean();
?>