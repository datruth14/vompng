<?php
$pageTitle = 'Orders - vomp';
ob_start();
?>
<section class="py-8 md:py-12">
    <div class="mb-10">
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Activity</p>
        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2">Orders & Activity</h1>
        <p class="text-gray-400">Track Vomp Coin usage and order activity across your stores.</p>
    </div>

    <?php if (empty($stores)): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
            </div>
            <h2 class="text-2xl font-black text-white mb-2">No Stores Yet</h2>
            <p class="text-gray-400">Create a store to start tracking orders and activity.</p>
            <a href="/dashboard" class="inline-block mt-6 px-6 py-3 rounded-2xl bg-[#ff610a] text-white font-black hover:bg-[#e05500] transition-all">Go to Dashboard</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-6">
            <?php foreach ($stores as $s):
                $txns = token_history($s['id'], 20);
                $productList = product_get_products_by_store($s['id']);
                $availableCount = count(array_filter($productList, fn($p) => (int)$p['is_available'] === 1));
            ?>
                <div class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-white/10">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#ff610a] to-purple-600 flex items-center justify-center text-white font-black shadow-lg shadow-[#ff610a]/20 flex-shrink-0">
                                <?php echo strtoupper(substr(htmlspecialchars($s['name']), 0, 1)); ?>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-white"><?php echo htmlspecialchars($s['name']); ?></h2>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">
                                    <?php echo $availableCount; ?> active products · <?php echo (int)($currentUser['token_balance'] ?? 0); ?> Vomp Coins
                                </p>
                            </div>
                        </div>
                        <a href="/dashboard/<?php echo htmlspecialchars($s['slug']); ?>" class="text-xs text-[#ff610a] hover:text-[#ff8c3a] font-black uppercase tracking-wider transition-colors">View Store</a>
                    </div>

                    <?php if (empty($txns)): ?>
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-sm">No activity yet. Vomp Coins will appear here as you use them.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($txns as $tx): ?>
                                <div class="flex items-center justify-between py-2.5 px-4 rounded-xl bg-white/[0.02] border border-white/5">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <?php if ($tx['type'] === 'credit'): ?>
                                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" /></svg>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" /></svg>
                                            </div>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-white truncate"><?php echo htmlspecialchars($tx['description'] ?? ($tx['type'] === 'credit' ? 'Vomp Coin purchase' : 'Vomp Coin deduction')); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($tx['created_at']); ?></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-black flex-shrink-0 ml-3 <?php echo $tx['type'] === 'credit' ? 'text-emerald-400' : 'text-rose-400'; ?>">
                                        <?php echo $tx['type'] === 'credit' ? '+' : '-'; ?><?php echo (int)$tx['amount']; ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($txns) >= 20): ?>
                            <a href="/dashboard/<?php echo htmlspecialchars($s['slug']); ?>/tokens" class="block text-center mt-4 text-xs text-[#ff610a] hover:text-[#ff8c3a] font-black uppercase tracking-wider transition-colors">View All Transactions</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
?>
