<?php
$pageTitle = 'Token Management - VomP';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-indigo-400 mb-2">Order Credits</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">Tokens</h1>
            <p class="text-gray-500 font-medium text-lg">Manage your storefront's order processing capacity.</p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article class="glass-morphism rounded-[2.5rem] p-10 border border-white/10 md:col-span-1 flex flex-col items-center justify-center text-center">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-4">Current Balance</p>
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-indigo-500/20 blur-3xl rounded-full"></div>
                <p class="text-7xl font-black text-white relative"><?php echo (int) $store['token_balance']; ?></p>
            </div>
            <p class="text-gray-400 text-sm font-medium">Tokens available for <br>customer orders</p>
        </article>

        <div class="md:col-span-2 space-y-8">
            <h2 class="text-2xl font-black text-white">Top Up Tokens</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <?php foreach ($plans as $key => $plan): ?>
                    <article class="glass-morphism rounded-3xl p-8 border border-white/10 hover:border-indigo-500/50 transition-all group">
                        <p class="text-xs uppercase tracking-[0.2em] font-black text-indigo-400 mb-2"><?php echo htmlspecialchars($plan['label']); ?></p>
                        <h3 class="text-3xl font-black text-white mb-4"><?php echo number_format($plan['tokens']); ?> <span class="text-sm text-gray-500">Tokens</span></h3>
                        <p class="text-2xl font-black text-white/90 mb-8">₦<?php echo number_format($plan['amount']); ?></p>
                        <button onclick="purchasePlan('<?php echo $key; ?>')" class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 text-white font-black text-sm group-hover:bg-indigo-500 group-hover:border-indigo-400 transition-all">Purchase Plan</button>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <section class="glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-white">Transaction History</h2>
            <div class="px-4 py-2 rounded-xl bg-white/5 text-xs font-black text-gray-400 uppercase tracking-widest">Last 50 Events</div>
        </div>

        <?php if (!$transactions): ?>
            <div class="py-12 text-center">
                <p class="text-gray-500 font-medium">No token activity recorded yet.</p>
            </div>
        <?php else: ?>
            <div class="overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/5 text-xs font-black text-gray-500 uppercase tracking-widest">
                            <th class="pb-4 pl-2">Description</th>
                            <th class="pb-4">Type</th>
                            <th class="pb-4">Amount</th>
                            <th class="pb-4 text-right pr-2">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($transactions as $tx): ?>
                            <tr class="group">
                                <td class="py-5 pl-2">
                                    <p class="text-white font-bold"><?php echo htmlspecialchars($tx['description'] ?: 'Token transaction'); ?></p>
                                    <p class="text-[10px] text-gray-600 font-mono mt-1 uppercase"><?php echo $tx['id']; ?></p>
                                </td>
                                <td class="py-5">
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-tighter <?php echo ($tx['type'] ?? 'debit') === 'credit' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'; ?>">
                                        <?php echo $tx['type'] ?? 'debit'; ?>
                                    </span>
                                </td>
                                <td class="py-5">
                                    <p class="font-black <?php echo ($tx['type'] ?? 'debit') === 'credit' ? 'text-emerald-400' : 'text-rose-400'; ?>">
                                        <?php echo ($tx['type'] ?? 'debit') === 'credit' ? '+' : '-'; ?><?php echo (int) $tx['amount']; ?>
                                    </p>
                                </td>
                                <td class="py-5 text-right pr-2 text-sm text-gray-500 font-medium">
                                    <?php echo date('M d, Y H:i', strtotime($tx['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>

<script>
async function purchasePlan(plan) {
    if (!confirm(`Confirm purchase of ${plan} plan?`)) return;

    const slug = '<?php echo $store['slug']; ?>';
    try {
        const res = await fetch(`/api/tokens/purchase?storeSlug=${slug}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ plan })
        });
        const result = await res.json();
        if (result.success) {
            alert('Purchase successful! Tokens added to your balance.');
            location.reload();
        } else {
            alert(result.error || 'Failed to complete purchase');
        }
    } catch (err) {
        alert('Network error. Please try again.');
    }
}
</script>

<?php
$content = ob_get_clean();
?>
