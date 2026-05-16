<?php
$pageTitle = 'Dashboard - VomP';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">Overview</h1>
            <p class="text-gray-500 font-medium text-lg">Manage your stores with the same visual rhythm as the original app.</p>
        </div>
        <a href="/register" class="btn-press px-8 py-4 rounded-2xl bg-indigo-500 text-white font-black text-sm shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all">Create Another Store</a>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Your Stores</p>
            <p class="text-5xl font-black text-white"><?php echo count($stores); ?></p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Total Products</p>
            <p class="text-5xl font-black text-white"><?php echo (int) $totalProducts; ?></p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Account Status</p>
            <p class="text-2xl font-black text-emerald-400">Active</p>
        </article>
    </div>

    <section class="glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10">
        <h2 class="text-2xl font-black text-white mb-6">Your Stores</h2>
        <?php if ($stores): ?>
            <div class="space-y-4">
                <?php foreach ($stores as $store): ?>
                    <article class="p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-black text-white"><?php echo htmlspecialchars($store['name']); ?></h3>
                                <p class="text-xs uppercase tracking-widest text-indigo-400 font-black mt-1"><?php echo htmlspecialchars($store['slug']); ?></p>
                                <p class="text-sm text-gray-400 mt-3"><?php echo htmlspecialchars($store['description'] ?: 'No description yet.'); ?></p>
                            </div>
                            <div class="flex gap-3">
                                <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>" class="px-5 py-2.5 rounded-xl bg-white/10 text-white text-sm font-black hover:bg-white/20 transition-all">View</a>
                                <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>" class="btn-press px-5 py-2.5 rounded-xl bg-indigo-500 text-white text-sm font-black hover:bg-indigo-400 transition-all">Manage</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-400">No stores yet. Create one to get started.</p>
        <?php endif; ?>
    </section>
</section>
<?php
$content = ob_get_clean();
?>
