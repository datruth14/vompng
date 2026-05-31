<?php
/*
 * Dashboard overview template for a single store.
 */

$pageTitle = 'Store Overview - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Managing <?php echo htmlspecialchars($store['slug']); ?></p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2"><?php echo htmlspecialchars($store['name']); ?></h1>
            <p class="text-gray-500 font-medium text-lg"><?php echo htmlspecialchars($store['description'] ?: 'No store description yet.'); ?></p>
        </div>
        <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>" target="_blank" class="btn-secondary px-8 py-4 rounded-2xl">Open Storefront</a>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Vomp Coin Balance</p>
            <p class="text-4xl md:text-5xl font-black text-white whitespace-nowrap"><?php echo number_format((int) ($currentUser['token_balance'] ?? 0)); ?></p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Live Products</p>
            <p class="text-4xl md:text-5xl font-black text-white break-all"><?php echo count(array_filter($products, fn($p) => (int) ($p['is_available'] ?? 1) === 1)); ?></p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Current Plan</p>
            <?php if (($currentUser['plan'] ?? 'free') === 'premium'): ?>
                <p class="text-3xl font-black text-emerald-400">PREMIUM</p>
            <?php else: ?>
                <p class="text-3xl font-black text-[#ff8c3a]">FREE</p>
            <?php endif; ?>
        </article>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/products" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
            <h3 class="text-white font-black text-xl mb-2">Manage Products</h3>
            <p class="text-gray-400 text-sm">Add, edit, publish, or hide items in your catalog.</p>
        </a>
        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/settings" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
            <h3 class="text-white font-black text-xl mb-2">Store Settings</h3>
            <p class="text-gray-400 text-sm">Update your WhatsApp, theme colors, and profile details.</p>
        </a>
        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/tokens" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
            <h3 class="text-white font-black text-xl mb-2">Vomp Coins</h3>
            <p class="text-gray-400 text-sm">Top up and review Vomp Coin transaction history.</p>
        </a>
    </div>

    <section class="glass-morphism rounded-[2.5rem] p-8 border border-white/10">
        <h2 class="text-2xl font-black text-white mb-6">Recent Vomp Coin Activity</h2>
        <?php if (!$transactions): ?>
            <p class="text-gray-400">No Vomp Coin activity yet.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($transactions as $tx): ?>
                    <article class="p-4 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-white font-bold"><?php echo htmlspecialchars($tx['description'] ?: 'Vomp Coin transaction'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($tx['created_at']); ?></p>
                        </div>
                        <span class="font-black <?php echo ($tx['type'] ?? 'debit') === 'credit' ? 'text-emerald-300' : 'text-rose-300'; ?>">
                            <?php echo ($tx['type'] ?? 'debit') === 'credit' ? '+' : '-'; ?><?php echo (int) $tx['amount']; ?>
                        </span>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
<?php
$content = ob_get_clean();
?>