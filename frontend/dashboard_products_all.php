<?php
$pageTitle = 'My Products - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate__animated animate__fadeInDown">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Dashboard</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">My Products</h1>
            <p class="text-gray-500 font-medium text-lg"><?php echo number_format((int) $totalProductsAll); ?> product<?php echo $totalProductsAll !== 1 ? 's' : ''; ?> across all stores</p>
        </div>
    </header>

    <?php if (!$products): ?>
        <div class="text-center py-16 animate__animated animate__fadeInUp">
            <div class="w-20 h-20 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 13.5V7.5" /></svg>
            </div>
            <h2 class="text-2xl font-black text-white mb-2">No Products Yet</h2>
            <p class="text-gray-400 mb-6">Create your first product to start selling.</p>
            <a href="/dashboard/<?php echo htmlspecialchars($stores[0]['slug'] ?? ''); ?>/products" class="inline-block px-6 py-3 rounded-2xl bg-[#ff610a] text-white font-black hover:bg-[#e05500] transition-all">Go to Products</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $cardDelay = 0; foreach ($products as $p): ?>
                <article class="glass-morphism rounded-[2rem] overflow-hidden border border-white/10 hover:bg-white/[0.02] transition-all animate__animated animate__fadeInUp" style="animation-delay:<?php echo $cardDelay * 0.1; ?>s" <?php $cardDelay++; ?>>
                    <div class="aspect-[4/3] bg-white/5 overflow-hidden">
                        <?php if (!empty($p['media_url'])): ?>
                            <img src="<?php echo htmlspecialchars(img_url($p['media_url'])); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-gray-600\'><svg class=\'w-10 h-10\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.5\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z\'/></svg></div>'">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-600">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" /></svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="text-lg font-black text-white leading-tight"><?php echo htmlspecialchars($p['name']); ?></h3>
                            <span class="text-lg font-black text-[#ff610a] flex-shrink-0"><?php echo htmlspecialchars(product_get_currency_symbol($p['currency'] ?? 'NGN')); ?><?php echo number_format((float) $p['price']); ?></span>
                        </div>
                        <a href="/store/<?php echo htmlspecialchars($p['store_slug']); ?>" class="text-xs text-[#ff610a] font-black uppercase tracking-wider hover:underline"><?php echo htmlspecialchars($p['store_name']); ?></a>
                        <div class="flex items-center gap-3 mt-3 text-xs text-gray-500 font-bold">
                            <span><?php echo htmlspecialchars($p['category'] ?: 'General'); ?></span>
                            <span>&middot;</span>
                            <span class="<?php echo (int) ($p['is_available'] ?? 1) ? 'text-emerald-400' : 'text-rose-400'; ?>"><?php echo (int) ($p['is_available'] ?? 1) ? 'Available' : 'Hidden'; ?></span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/5">
                            <a href="/dashboard/<?php echo htmlspecialchars($p['store_slug']); ?>/products" class="text-sm text-white font-bold hover:text-[#ff610a] transition-colors">Manage in Store &rarr;</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center gap-3 mt-8">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-5 py-3 rounded-xl bg-white/10 text-white font-black text-sm hover:bg-white/20 transition-all">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-5 py-3 rounded-xl bg-[#ff610a] text-white font-black text-sm hover:bg-[#e05500] transition-all">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
?>
