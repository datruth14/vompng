<?php
$pageTitle = 'Marketplace - vomp';
ob_start();
?>
<section class="py-8 md:py-12">
    <!-- Header -->
    <div class="mb-8">
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Browse Products</p>
        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2">Marketplace</h1>
        <p class="text-gray-400">Discover products from stores across Nigeria.</p>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="/marketplace" class="mb-6">
        <div class="relative max-w-xl">
            <input type="text" name="q" value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>" placeholder="Search products or stores..." class="w-full rounded-xl px-5 py-3.5 pl-12 bg-transparent border border-white/10 focus:border-[#ff610a] focus:outline-none text-white placeholder-gray-500 transition-colors" />
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            <?php if ($searchQuery): ?>
                <a href="/marketplace" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full text-gray-500 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </a>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($searchQuery): ?>
        <p class="text-sm text-gray-400 mb-6">
            Search results for "<strong class="text-white"><?php echo htmlspecialchars($searchQuery); ?></strong>"
            — <?php echo count($allProducts); ?> product<?php echo count($allProducts) !== 1 ? 's' : ''; ?>
            <?php if ($searchStores): ?>
                , <?php echo count($searchStores); ?> store<?php echo count($searchStores) !== 1 ? 's' : ''; ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <!-- Category Pills -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent -mx-4 px-4 snap-x snap-mandatory">
        <a href="/marketplace"
           class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all border <?php echo !$activeCategory ? 'bg-[#ff610a] text-white border-[#ff610a]' : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10 hover:text-white'; ?>">
            All
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="/marketplace?category=<?php echo urlencode($cat); ?>"
               class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all border <?php echo $activeCategory === $cat ? 'bg-[#ff610a] text-white border-[#ff610a]' : 'bg-white/5 text-gray-400 border-white/10 hover:bg-white/10 hover:text-white'; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Products Heading -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-1">Products</p>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                <?php echo $activeCategory ? htmlspecialchars($activeCategory) : ($searchQuery ? 'Search Results' : 'All Products'); ?>
            </h2>
        </div>
        <?php if (!empty($allProducts)): ?>
                <a href="/products" class="inline-flex items-center gap-2 text-xs text-[#ff610a] hover:text-[#ff8c3a] font-black uppercase tracking-wider transition-colors">
                More Products
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
            </a>
        <?php endif; ?>
    </div>

    <!-- Products Grid -->
    <?php if (empty($allProducts)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 11.625l2.25-2.25M12 11.625l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
            </div>
            <h2 class="text-2xl font-black text-white mb-2">No Products Found</h2>
            <p class="text-gray-400"><?php echo $activeCategory ? 'No products in this category yet.' : 'No products available yet.'; ?></p>
            <?php if (empty($currentUser)): ?>
                <a href="/register" class="inline-block mt-6 px-6 py-3 rounded-2xl bg-[#ff610a] text-white font-black hover:bg-[#e05500] transition-all">Start Selling</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($allProducts as $p): ?>
                <a href="/store/<?php echo htmlspecialchars($p['store_slug']); ?>/<?php echo htmlspecialchars($p['id']); ?>" class="glass-morphism rounded-2xl border border-white/10 overflow-hidden hover:bg-white/[0.03] transition-all group">
                    <div class="aspect-square skeleton-box relative overflow-hidden">
                        <?php if (!empty($p['media_url'])): ?>
                            <img src="<?php echo htmlspecialchars(img_url($p['media_url'])); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="img-skeleton w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')" />
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
    <?php endif; ?>

    <!-- Searched Stores (when searching) -->
    <?php if (!empty($searchStores)): ?>
        <section class="mt-10 mb-10">
            <h2 class="text-2xl font-black text-white tracking-tight mb-4">Stores</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($searchStores as $s):
                    $availableCount = count(array_filter(product_get_products_by_store($s['id']), fn($p) => (int)$p['is_available'] === 1));
                ?>
                    <a href="/store/<?php echo htmlspecialchars($s['slug']); ?>" class="glass-morphism rounded-2xl p-5 border border-white/10 hover:bg-white/[0.03] transition-all group">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#ff610a] to-purple-600 flex items-center justify-center text-white font-black shadow-lg shadow-[#ff610a]/20 flex-shrink-0 text-lg">
                                <?php echo strtoupper(substr(htmlspecialchars($s['name']), 0, 1)); ?>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-black text-white group-hover:text-[#ff610a] transition-colors truncate"><?php echo htmlspecialchars($s['name']); ?></h3>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider"><?php echo $availableCount; ?> product<?php echo $availableCount !== 1 ? 's' : ''; ?></p>
                            </div>
                        </div>
                        <?php if (!empty($s['description'])): ?>
                            <p class="text-gray-400 text-sm leading-relaxed line-clamp-2"><?php echo htmlspecialchars($s['description']); ?></p>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Explore Storefronts -->
    <?php if (!empty($stores)): ?>
        <section class="mt-16 md:mt-20">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-1">Vendors</p>
                    <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Explore Storefronts</h2>
                </div>
                <a href="/stores" class="inline-flex items-center gap-2 text-xs text-[#ff610a] hover:text-[#ff8c3a] font-black uppercase tracking-wider transition-colors">
                    All Stores
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                <?php foreach ($stores as $s):
                    $hasHero = !empty($s['hero_image_url']);
                ?>
                    <a href="/store/<?php echo htmlspecialchars($s['slug']); ?>" class="rounded-3xl p-6 border border-white/10 hover:border-[#ff610a]/30 transition-all group block relative overflow-hidden min-h-[200px] <?php echo $hasHero ? '' : 'glass-morphism'; ?>">
                        <?php if ($hasHero): ?>
                        <div class="absolute inset-0 skeleton-box overflow-hidden">
                            <img src="<?php echo htmlspecialchars(img_url($s['hero_image_url'])); ?>" alt="" class="img-skeleton w-full h-full object-cover" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')" />
                        </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 <?php echo $hasHero ? 'z-10 bg-gradient-to-t from-black/80 via-black/40 to-transparent' : 'bg-gradient-to-t from-black/60 via-black/20 to-transparent'; ?>"></div>
                        <div class="relative <?php echo $hasHero ? 'z-20' : 'z-10'; ?> flex flex-col h-full min-h-[170px]">
                            <div class="w-14 h-14 rounded-2xl bg-[#ff610a]/20 backdrop-blur-sm flex items-center justify-center text-xl font-black text-[#ff610a] mb-3">
                                <?php echo strtoupper($s['name'][0]); ?>
                            </div>
                            <div class="mt-auto">
                                <h3 class="text-xl font-black text-white group-hover:text-[#ff610a] transition-colors mb-1"><?php echo htmlspecialchars($s['name']); ?></h3>
                                <?php if ($s['description']): ?>
                                    <p class="text-sm <?php echo $hasHero ? 'text-gray-300' : 'text-gray-400'; ?> line-clamp-2"><?php echo htmlspecialchars($s['description']); ?></p>
                                <?php endif; ?>
                                <div class="flex items-center gap-2 mt-4 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                                    <span>Visit Store →</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
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
