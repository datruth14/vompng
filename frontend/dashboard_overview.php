<?php
/*
 * Dashboard overview template for a single store.
 */

$pageTitle = 'Store Overview - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate__animated animate__fadeInDown">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Managing <?php echo htmlspecialchars($store['slug']); ?></p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2"><?php echo htmlspecialchars($store['name']); ?> <?php if (($currentUser['plan'] ?? 'free') === 'premium'): ?><span class="inline-block align-middle text-xs font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full">PREMIUM</span><?php endif; ?></h1>
            <p class="text-gray-500 font-medium text-lg"><?php echo htmlspecialchars($store['description'] ?: 'No store description yet.'); ?></p>
        </div>
        <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>" target="_blank" class="btn-secondary px-8 py-4 rounded-2xl">Open Storefront</a>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 animate__animated animate__fadeInUp" style="animation-delay:0s">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Vomp Coin Balance</p>
            <p class="text-4xl md:text-5xl font-black text-white text-fit"><?php echo number_format((int) ($currentUser['token_balance'] ?? 0)); ?></p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 animate__animated animate__fadeInUp" style="animation-delay:0.1s">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Orders</p>
            <p class="text-4xl md:text-5xl font-black text-white"><?php echo number_format($orderCount); ?></p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 animate__animated animate__fadeInUp" style="animation-delay:0.2s">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Live Products</p>
            <p class="text-4xl md:text-5xl font-black text-white break-all"><?php echo count(array_filter($products, fn($p) => (int) ($p['is_available'] ?? 1) === 1)); ?></p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 animate__animated animate__fadeInUp" style="animation-delay:0.3s">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Today's Visits</p>
            <p class="text-4xl md:text-5xl font-black text-purple-400"><?php echo number_format($todayVisits); ?></p>
            <p class="text-xs text-gray-500 mt-2">All time: <?php echo number_format((int) ($store['visits'] ?? 0)); ?></p>
        </article>
    </div>

    <!-- 7-Day Chart -->
    <div class="glass-morphism rounded-[2.5rem] p-8 border border-white/10 animate__animated animate__fadeInUp">
        <h2 class="text-2xl font-black text-white mb-6">Last 7 Days</h2>
        <div class="relative" style="max-height:260px">
            <canvas id="storeChart"></canvas>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
        (function() {
            var ctx = document.getElementById('storeChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chartLabels); ?>,
                    datasets: [{
                        label: 'Visits',
                        data: <?php echo json_encode($chartVisits); ?>,
                        backgroundColor: 'rgba(168,85,247,0.6)',
                        borderColor: '#a855f7',
                        borderWidth: 1,
                        borderRadius: 4
                    }, {
                        label: 'Orders',
                        data: <?php echo json_encode($chartOrders); ?>,
                        backgroundColor: 'rgba(34,197,94,0.6)',
                        borderColor: '#22c55e',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#9ca3af', font: { weight: 'bold', size: 11 } }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#6b7280', font: { size: 11 } },
                            grid: { color: 'rgba(255,255,255,0.05)' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#6b7280', font: { size: 11 }, stepSize: 1 },
                            grid: { color: 'rgba(255,255,255,0.05)' }
                        }
                    }
                }
            });
        })();
        </script>
    </div>

    <div class="grid md:grid-cols-4 gap-6">
        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/products" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all animate__animated animate__fadeInUp" style="animation-delay:0.1s">
            <h3 class="text-white font-black text-xl mb-2">Manage Products</h3>
            <p class="text-gray-400 text-sm">Add, edit, publish, or hide items in your catalog.</p>
        </a>
        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/orders" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all animate__animated animate__fadeInUp" style="animation-delay:0.2s">
            <h3 class="text-white font-black text-xl mb-2">Orders</h3>
            <p class="text-gray-400 text-sm">View customer orders placed via your storefront.</p>
        </a>
        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/settings" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all animate__animated animate__fadeInUp" style="animation-delay:0.3s">
            <h3 class="text-white font-black text-xl mb-2">Store Settings</h3>
            <p class="text-gray-400 text-sm">Update your WhatsApp, theme colors, and profile details.</p>
        </a>
        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/tokens" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all animate__animated animate__fadeInUp" style="animation-delay:0.4s">
            <h3 class="text-white font-black text-xl mb-2">Vomp Coins</h3>
            <p class="text-gray-400 text-sm">Top up and review Vomp Coin transaction history.</p>
        </a>
    </div>

    <section class="glass-morphism rounded-[2.5rem] p-8 border border-white/10 animate__animated animate__fadeInUp">
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
                            <?php echo ($tx['type'] ?? 'debit') === 'credit' ? '+' : '-'; ?><?php echo number_format((int) $tx['amount']); ?>
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