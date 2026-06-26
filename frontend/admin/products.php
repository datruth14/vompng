<?php
$pageTitle = 'Admin Products - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header>
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Super Admin / Products</p>
        <h1 class="text-5xl font-black text-white tracking-tight">All Products</h1>
    </header>

    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" class="flex gap-3 flex-1 max-w-md">
            <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Search by product, store, or category..." class="flex-1 bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm">
            <button type="submit" class="px-5 py-3 rounded-2xl bg-[#ff610a] text-white font-bold text-sm hover:bg-[#e05500] transition-all">Search</button>
            <?php if (!empty($_GET['q'])): ?>
                <a href="/admin/products" class="px-5 py-3 rounded-2xl bg-white/5 border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/10 transition-all">Clear</a>
            <?php endif; ?>
        </form>
        <a href="/api/admin/export?type=products" class="px-5 py-3 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 font-bold text-sm hover:bg-emerald-600/30 transition-all whitespace-nowrap">Export CSV</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 uppercase tracking-wider text-xs font-black">
                    <th class="text-left p-3">Product</th>
                    <th class="text-left p-3">Price</th>
                    <th class="text-left p-3">Store</th>
                    <th class="text-left p-3">Category</th>
                    <th class="text-left p-3">Available</th>
                    <th class="text-left p-3">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                        <td class="p-3 text-white font-bold"><?php echo htmlspecialchars($p['name']); ?></td>
                        <td class="p-3 text-[#ff8c3a] font-bold"><?php echo htmlspecialchars(product_get_currency_symbol($p['currency'] ?? 'NGN')); ?><?php echo number_format((float) $p['price'], 2); ?></td>
                        <td class="p-3">
                            <a href="/store/<?php echo htmlspecialchars($p['slug'] ?? $p['store_slug'] ?? ''); ?>" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                                <?php echo htmlspecialchars($p['store_name'] ?? '-'); ?>
                            </a>
                        </td>
                        <td class="p-3 text-gray-400"><?php echo htmlspecialchars($p['category'] ?? 'Others'); ?></td>
                        <td class="p-3">
                            <?php if ((int) ($p['is_available'] ?? 1) === 1): ?>
                                <span class="text-emerald-400 font-bold">Yes</span>
                            <?php else: ?>
                                <span class="text-rose-400 font-bold">No</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-gray-500 text-xs whitespace-nowrap"><?php echo date('M j, Y', strtotime($p['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$products): ?>
                    <tr><td colspan="6" class="p-6 text-center text-gray-500">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-2 mt-8">
            <?php $qs = !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?>
            <?php if ($page > 1): ?>
                <a href="/admin/products?page=<?php echo $page - 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="/admin/products?page=<?php echo $i . $qs; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="/admin/products?page=<?php echo $page + 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
?>
