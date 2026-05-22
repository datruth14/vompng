<?php
/*
 * Dashboard landing page template with store list and stats.
 */

$pageTitle = 'Dashboard - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">Overview</h1>
            <p class="text-gray-500 font-medium text-lg">Manage your stores with the same visual rhythm as the original app.</p>
        </div>
        <?php if (($currentUser['plan'] ?? 'free') === 'premium' || count($stores) === 0): ?>
            <a href="/dashboard/create-store" class="px-8 py-4 rounded-2xl bg-gray-950 text-white font-black text-sm border border-white/10 hover:bg-white/5 transition-all">Create Another Store</a>
        <?php else: ?>
            <div class="text-right">
                <p class="text-xs text-gray-500 font-bold">Upgrade to premium to create more stores</p>
            </div>
        <?php endif; ?>
    </header>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        <a href="/dashboard/stores" class="glass-morphism rounded-[2rem] p-8 border border-white/10 block hover:bg-white/[0.03] transition-all">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Your Stores</p>
            <p class="text-5xl font-black text-white"><?php echo count($stores); ?></p>
        </a>
        <a href="/dashboard/products" class="glass-morphism rounded-[2rem] p-8 border border-white/10 block hover:bg-white/[0.03] transition-all">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Total Products</p>
            <p class="text-5xl font-black text-white"><?php echo (int) $totalProducts; ?></p>
        </a>
        <a href="/dashboard/<?php echo htmlspecialchars($stores[0]['slug'] ?? ''); ?>/tokens" class="glass-morphism rounded-[2rem] p-8 border border-white/10 block hover:bg-white/[0.03] transition-all">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Vomp Coin Balance</p>
            <p class="text-5xl font-black text-[#ff610a]"><?php echo (int) ($currentUser['token_balance'] ?? 0); ?></p>
        </a>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Current Plan</p>
            <?php if (($currentUser['plan'] ?? 'free') === 'premium'): ?>
                <p class="text-3xl font-black text-emerald-400">PREMIUM</p>
            <?php else: ?>
                <p class="text-3xl font-black text-[#ff8c3a] mb-3">FREE</p>
                <button onclick="upgradeToPremium()" class="w-full px-4 py-3 rounded-xl bg-[#ff610a] text-white font-black text-xs hover:bg-[#e05500] transition-all">Upgrade to Premium — 500 Vomp Coins</button>
                <div id="upgradeMsg" class="mt-2"></div>
            <?php endif; ?>
        </article>
    </div>

    <section class="glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10">
        <h2 class="text-2xl font-black text-white mb-6">Your Stores</h2>
        <?php if ($stores): ?>
            <div class="space-y-4">
                <?php foreach (array_slice($stores, 0, 3) as $store): ?>
                    <article class="p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-black text-white"><?php echo htmlspecialchars($store['name']); ?></h3>
                                <p class="text-xs uppercase tracking-widest text-[#ff610a] font-black mt-1"><?php echo htmlspecialchars($store['slug']); ?></p>
                                <p class="text-sm text-gray-400 mt-3"><?php echo htmlspecialchars($store['description'] ?: 'No description yet.'); ?></p>
                            </div>
                            <div class="flex gap-3">
                                <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>" class="px-5 py-2.5 rounded-xl bg-white/10 text-white text-sm font-black hover:bg-white/20 transition-all">View</a>
                                <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>" class="btn-press px-5 py-2.5 rounded-xl bg-[#ff610a] text-white text-sm font-black hover:bg-[#e05500] transition-all">Manage</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if (count($stores) > 3): ?>
                <div class="mt-6 text-center">
                    <a href="/dashboard/stores" class="inline-block px-8 py-4 rounded-2xl bg-white/10 text-white font-black text-sm hover:bg-white/20 transition-all">View All Stores (<?php echo count($stores); ?>)</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-gray-400">No stores yet. Create one to get started.</p>
        <?php endif; ?>
    </section>
</section>

<script>
async function upgradeToPremium() {
    const btn = document.querySelector('button[onclick="upgradeToPremium()"]');
    const msg = document.getElementById('upgradeMsg');
    btn.disabled = true;
    btn.textContent = 'Processing...';
    try {
        const res = await fetch('/api/upgrade', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ storeSlug: '' })
        });
        const result = await res.json();
        if (result.success) {
            msg.innerHTML = '<div class="px-3 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-bold">Upgraded to Premium!</div>';
            setTimeout(() => location.reload(), 1500);
        } else {
            msg.innerHTML = '<div class="px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-bold">' + (result.error || 'Upgrade failed') + '</div>';
            btn.disabled = false;
            btn.textContent = 'Upgrade to Premium — 500 Vomp Coins';
        }
    } catch (err) {
        msg.innerHTML = '<div class="px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-bold">Network error</div>';
        btn.disabled = false;
        btn.textContent = 'Upgrade to Premium — 500 Vomp Coins';
    }
}
</script>

<?php
$content = ob_get_clean();
?>
