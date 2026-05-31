<?php
$pageTitle = 'Admin Stores - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header>
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Super Admin / Stores</p>
        <h1 class="text-5xl font-black text-white tracking-tight">All Stores</h1>
    </header>

    <?php if ($success): ?>
        <div class="px-4 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-bold"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="px-4 py-3 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" class="flex gap-3 flex-1 max-w-md">
            <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Search stores, owners..." class="flex-1 bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm">
            <button type="submit" class="px-5 py-3 rounded-2xl bg-[#ff610a] text-white font-bold text-sm hover:bg-[#e05500] transition-all">Search</button>
            <?php if (!empty($_GET['q'])): ?>
                <a href="/admin/stores" class="px-5 py-3 rounded-2xl bg-white/5 border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/10 transition-all">Clear</a>
            <?php endif; ?>
        </form>
        <a href="/api/admin/export?type=stores" class="px-5 py-3 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 font-bold text-sm hover:bg-emerald-600/30 transition-all whitespace-nowrap">Export CSV</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 uppercase tracking-wider text-xs font-black">
                    <th class="text-left p-3">Store</th>
                    <th class="text-left p-3">Owner</th>
                    <th class="text-left p-3">Products</th>
                    <th class="text-left p-3">Tokens</th>
                    <th class="text-left p-3">Plan</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Created</th>
                    <th class="text-left p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stores as $s): ?>
                    <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                        <td class="p-3">
                            <a href="/store/<?php echo htmlspecialchars($s['slug']); ?>" target="_blank" class="text-white font-bold hover:text-[#ff610a] transition-colors"><?php echo htmlspecialchars($s['name']); ?></a>
                        </td>
                        <td class="p-3 text-gray-400"><?php echo htmlspecialchars($s['owner_name'] ?? $s['owner_email'] ?? '-'); ?></td>
                        <td class="p-3 text-white"><?php echo number_format((int) ($s['product_count'] ?? 0)); ?></td>
                        <td class="p-3 text-white"><?php echo number_format((int) ($s['token_balance'] ?? 0)); ?></td>
                        <td class="p-3">
                            <?php if (($s['plan'] ?? 'free') === 'premium'): ?>
                                <span class="text-emerald-400 font-bold">PREMIUM</span>
                            <?php else: ?>
                                <span class="text-gray-500">free</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3">
                            <?php if ((int) ($s['is_active'] ?? 1) === 1): ?>
                                <span class="text-emerald-400 font-bold">Active</span>
                            <?php else: ?>
                                <span class="text-rose-400 font-bold">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-gray-500 text-xs whitespace-nowrap"><?php echo date('M j, Y', strtotime($s['created_at'])); ?></td>
                        <td class="p-3">
                            <form method="POST" action="/api/admin/toggle-store" class="inline">
                                <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($s['id']); ?>">
                                <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-all <?php echo (int) ($s['is_active'] ?? 1) === 1 ? 'bg-rose-500/10 border-rose-500/30 text-rose-300 hover:bg-rose-500/20' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300 hover:bg-emerald-500/20'; ?>">
                                    <?php echo (int) ($s['is_active'] ?? 1) === 1 ? 'Disable' : 'Enable'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$stores): ?>
                    <tr><td colspan="8" class="p-6 text-center text-gray-500">No stores found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-2 mt-8">
            <?php $qs = !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?>
            <?php if ($page > 1): ?>
                <a href="/admin/stores?page=<?php echo $page - 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="/admin/stores?page=<?php echo $i . $qs; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="/admin/stores?page=<?php echo $page + 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
?>
