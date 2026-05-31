<?php
$pageTitle = 'All Products - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Browse</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">All Products</h1>
            <p class="text-gray-500 font-medium text-lg">Discover products from stores across Nigeria.</p>
        </div>
    </header>

    <form method="GET" action="/products" class="relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        <input type="text" name="q" value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>" placeholder="Search products..." class="w-full rounded-xl px-5 py-3.5 pl-12 bg-transparent border border-white/10 focus:border-[#ff610a] focus:outline-none text-white placeholder-gray-500 transition-colors"/>
    </form>

    <?php if ($searchQuery): ?>
        <p class="text-sm text-gray-400"><?php echo number_format((int) $totalProducts); ?> result<?php echo $totalProducts !== 1 ? 's' : ''; ?> for "<span class="text-white font-bold"><?php echo htmlspecialchars($searchQuery); ?></span>"</p>
    <?php elseif ($totalProducts > $perPage): ?>
        <p class="text-sm text-gray-400">Page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo number_format($totalProducts); ?> products)</p>
    <?php endif; ?>

    <!-- Category Pills -->
    <?php if (!empty($categories)): ?>
    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent -mx-4 px-4 snap-x snap-mandatory">
        <a href="/products"
           class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all border snap-start <?php echo !$activeCategory ? 'bg-[#ff610a] text-white border-[#ff610a]' : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10 hover:text-white'; ?>">
            All
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="/products?category=<?php echo urlencode($cat); ?>"
               class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all border snap-start <?php echo $activeCategory === $cat ? 'bg-[#ff610a] text-white border-[#ff610a]' : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10 hover:text-white'; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div class="py-16 text-center">
            <h2 class="text-2xl font-black text-white mb-2">No Products Found</h2>
            <p class="text-gray-400"><?php echo $activeCategory ? 'No products in this category yet.' : 'No products available yet.'; ?></p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($products as $p): ?>
                <a href="/store/<?php echo htmlspecialchars($p['store_slug']); ?>/<?php echo htmlspecialchars($p['id']); ?>" class="glass-morphism rounded-2xl border border-white/10 overflow-hidden hover:bg-white/[0.03] transition-all group">
                    <div class="aspect-square skeleton-box relative overflow-hidden">
                        <?php if (!empty($p['media_url'])): ?>
                            <img src="<?php echo htmlspecialchars(img_url($p['media_url'])); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="img-skeleton w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')"/>
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-600">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-0.5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-[10px] font-black uppercase tracking-wider">In Stock</span>
                        </div>
                    </div>
                    <div class="p-3 md:p-4">
                        <p class="text-sm md:text-lg font-black text-white mb-1">₦<?php echo number_format((float)$p['price']); ?></p>
                        <p class="text-xs md:text-sm text-gray-300 font-semibold truncate mb-1"><?php echo htmlspecialchars($p['name']); ?></p>
                        <div class="flex items-center gap-1 text-[10px] md:text-xs text-gray-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
                            <span class="truncate"><?php echo htmlspecialchars($p['store_name']); ?></span>
                        </div>
                        <?php if (!empty($p['location'])): ?>
                            <p class="text-[10px] text-gray-600 mt-0.5"><?php echo htmlspecialchars($p['location']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($p['product_condition'])): ?>
                            <p class="text-[10px] text-gray-600 mt-0.5"><?php echo htmlspecialchars($p['product_condition']); ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="flex items-center justify-center gap-2 pt-4">
                <?php if ($page > 1): ?>
                    <a href="/products?<?php
                        $params = [];
                        if ($searchQuery) $params['q'] = urlencode($searchQuery);
                        if ($activeCategory) $params['category'] = urlencode($activeCategory);
                        $params['page'] = $page - 1;
                        echo http_build_query($params);
                    ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
                <?php endif; ?>
                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                    $params = [];
                    if ($searchQuery) $params['q'] = urlencode($searchQuery);
                    if ($activeCategory) $params['category'] = urlencode($activeCategory);
                    $params['page'] = $i;
                ?>
                    <a href="/products?<?php echo http_build_query($params); ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="/products?<?php
                        $params = [];
                        if ($searchQuery) $params['q'] = urlencode($searchQuery);
                        if ($activeCategory) $params['category'] = urlencode($activeCategory);
                        $params['page'] = $page + 1;
                        echo http_build_query($params);
                    ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<footer class="py-16 md:py-20 border-t border-white/5 mt-16">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">
        <div class="md:col-span-2">
            <img src="/assets/img/logo.png" alt="vomp" class="h-10 w-auto mb-4">
            <p class="text-gray-400 text-sm leading-relaxed max-w-sm">vomp is Nigeria's simplest marketplace platform. Create your store, list products, and receive orders directly via WhatsApp — no technical skills required.</p>
            <div class="flex items-center gap-3 mt-5">
                <a href="mailto:support@vomp.ng" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                </a>
                <a href="https://wa.me/2349115963439" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                <a href="https://www.youtube.com/@vompDotNg" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                <a href="https://www.facebook.com/VompNG" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="https://www.instagram.com/vomp.ng/" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
            </div>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-wider mb-4">Quick Links</h4>
            <ul class="space-y-3">
                <li><a href="/marketplace" class="text-gray-400 hover:text-white text-sm transition-colors">Marketplace</a></li>
                <?php if ($currentUser): ?>
                    <li><a href="/dashboard" class="text-gray-400 hover:text-white text-sm transition-colors">Dashboard</a></li>
                    <li><a href="/logout" class="text-gray-400 hover:text-white text-sm transition-colors">Logout</a></li>
                <?php else: ?>
                    <li><a href="/register" class="text-gray-400 hover:text-white text-sm transition-colors">Create a Store</a></li>
                    <li><a href="/login" class="text-gray-400 hover:text-white text-sm transition-colors">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-wider mb-4">Contact</h4>
            <ul class="space-y-3 text-sm text-gray-400">
                <li>support@vomp.ng</li>
                <li>(234) 9115 963 439</li>
                <li>Mowe, Ogun State, Nigeria.</li>
            </ul>
        </div>
    </div>
    <div class="max-w-6xl mx-auto mt-10 pt-6 border-t border-white/5 text-center text-xs text-gray-600">
        &copy; <?php echo date('Y'); ?> vomp. All rights reserved.
    </div>
    <div class="max-w-6xl mx-auto mt-3 text-center text-xs text-gray-700">
        vomp is a product of 14Eter Limited RC: 1865845
    </div>
</footer>

<?php
$content = ob_get_clean();
?>
