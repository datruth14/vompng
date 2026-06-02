<?php
$pageTitle = 'Orders - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header class="flex items-center justify-between gap-4 animate__animated animate__fadeInDown">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight mb-1">Orders</h1>
            <p class="text-gray-500 font-medium">Customer orders placed via your storefront.</p>
        </div>
        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>" class="btn-secondary px-6 py-3 rounded-2xl text-sm">Back</a>
    </header>

    <form method="GET" class="glass-morphism rounded-[2rem] p-6 border border-white/10 flex flex-col md:flex-row gap-4 animate__animated animate__fadeInUp">
        <div class="flex-1 min-w-0">
            <label class="text-xs uppercase tracking-wider font-black text-gray-500 mb-2 block">From</label>
            <input type="date" name="from" value="<?php echo htmlspecialchars($_GET['from'] ?? ''); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-[#ff610a]/50 transition-all">
        </div>
        <div class="flex-1 min-w-0">
            <label class="text-xs uppercase tracking-wider font-black text-gray-500 mb-2 block">To</label>
            <input type="date" name="to" value="<?php echo htmlspecialchars($_GET['to'] ?? ''); ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-[#ff610a]/50 transition-all">
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#ff610a] text-white font-bold text-sm hover:bg-[#e05500] transition-all">Filter</button>
            <?php if (!empty($_GET['from']) || !empty($_GET['to'])): ?>
                <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/orders" class="px-6 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Clear</a>
            <?php endif; ?>
            <a href="/api/orders_export.php?storeSlug=<?php echo htmlspecialchars($store['slug']); ?>&from=<?php echo htmlspecialchars($_GET['from'] ?? ''); ?>&to=<?php echo htmlspecialchars($_GET['to'] ?? ''); ?>" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-500 transition-all whitespace-nowrap">Export CSV</a>
        </div>
    </form>

    <?php if (!$orders): ?>
        <div class="glass-morphism rounded-[2.5rem] p-12 border border-white/10 text-center animate__animated animate__fadeInUp">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
            </div>
            <p class="text-xl font-black text-white mb-2">No orders yet</p>
            <p class="text-gray-400">When customers place orders, they will appear here.</p>
        </div>
    <?php else: ?>
        <div class="glass-morphism rounded-[2.5rem] border border-white/10 overflow-hidden animate__animated animate__fadeInUp">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-left">
                            <th class="px-6 py-4 text-xs uppercase tracking-wider font-black text-gray-500">Customer</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-wider font-black text-gray-500">Product</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-wider font-black text-gray-500">Email</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-wider font-black text-gray-500">Location</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-wider font-black text-gray-500">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition-all">
                                <td class="px-6 py-4 text-white font-bold"><?php echo htmlspecialchars($ord['customer_name']); ?></td>
                                <td class="px-6 py-4 text-gray-300"><?php echo htmlspecialchars($ord['product_name'] ?: 'General inquiry'); ?></td>
                                <td class="px-6 py-4 text-gray-400"><?php echo htmlspecialchars($ord['customer_email']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-300"><?php echo htmlspecialchars($ord['state']); ?></span>
                                    <span class="text-gray-500 text-xs block"><?php echo htmlspecialchars($ord['delivery_location']); ?></span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap"><?php echo htmlspecialchars($ord['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="flex items-center justify-center gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<style>
.btn-secondary { background:rgba(255,255,255,.08); color:#fff; border:1px solid rgba(255,255,255,.12); font-weight:800; }
.btn-secondary:hover { background: rgba(255,255,255,.12); }
</style>
<?php
$content = ob_get_clean();
?>