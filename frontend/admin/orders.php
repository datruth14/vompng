<?php
$pageTitle = 'Admin Token Log - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header>
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Super Admin / Token Log</p>
        <h1 class="text-5xl font-black text-white tracking-tight">Token Transactions</h1>
    </header>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 uppercase tracking-wider text-xs font-black">
                    <th class="text-left p-3">Store</th>
                    <th class="text-left p-3">Type</th>
                    <th class="text-left p-3">Amount</th>
                    <th class="text-left p-3">Description</th>
                    <th class="text-left p-3">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t): ?>
                    <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                        <td class="p-3">
                            <a href="/store/<?php echo htmlspecialchars($t['store_slug'] ?? ''); ?>" target="_blank" class="text-white font-bold hover:text-[#ff610a] transition-colors">
                                <?php echo htmlspecialchars($t['store_name'] ?? '-'); ?>
                            </a>
                        </td>
                        <td class="p-3">
                            <?php if ($t['type'] === 'credit'): ?>
                                <span class="text-emerald-400 font-bold">Credit</span>
                            <?php else: ?>
                                <span class="text-rose-400 font-bold">Debit</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-white font-bold"><?php echo ($t['type'] === 'credit' ? '+' : '-') . number_format((int) $t['amount']); ?></td>
                        <td class="p-3 text-gray-400 max-w-xs truncate"><?php echo htmlspecialchars($t['description'] ?? ''); ?></td>
                        <td class="p-3 text-gray-500 text-xs whitespace-nowrap"><?php echo date('M j, Y g:i A', strtotime($t['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$transactions): ?>
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">No transactions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-2 mt-8">
            <?php if ($page > 1): ?>
                <a href="/admin/orders?page=<?php echo $page - 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="/admin/orders?page=<?php echo $i; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="/admin/orders?page=<?php echo $page + 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
?>
