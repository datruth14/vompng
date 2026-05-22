<?php
$pageTitle = 'My Stores - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Dashboard</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">My Stores</h1>
            <p class="text-gray-500 font-medium text-lg"><?php echo count($stores); ?> store<?php echo count($stores) !== 1 ? 's' : ''; ?> total</p>
        </div>
        <a href="/dashboard/create-store" class="btn-press px-8 py-4 rounded-2xl bg-[#ff610a] text-white font-black text-sm shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">Create Another Store</a>
    </header>

    <div class="space-y-4">
        <?php foreach ($stores as $store):
            $productCount = count(product_get_products_by_store($store['id']));
        ?>
            <article class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-white/10 hover:bg-white/[0.02] transition-all">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#ff610a] to-purple-600 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-[#ff610a]/20 flex-shrink-0">
                            <?php echo strtoupper(substr(htmlspecialchars($store['name']), 0, 1)); ?>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-white"><?php echo htmlspecialchars($store['name']); ?></h2>
                            <p class="text-xs uppercase tracking-widest text-[#ff610a] font-black mt-0.5"><?php echo htmlspecialchars($store['slug']); ?></p>
                            <p class="text-sm text-gray-400 mt-2"><?php echo htmlspecialchars($store['description'] ?: 'No description yet.'); ?></p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-xs text-gray-500 font-mono truncate"><?php echo $_SERVER['HTTP_HOST'] ?? 'localhost'; ?>/store/<?php echo htmlspecialchars($store['slug']); ?></span>
                                <button onclick="shareStore('<?php echo htmlspecialchars($store['slug']); ?>', '<?php echo htmlspecialchars($store['name']); ?>')" class="flex-shrink-0 w-7 h-7 rounded-lg bg-black border border-[#ff610a]/30 text-[#ff610a] hover:bg-[#ff610a]/10 transition-all flex items-center justify-center" title="Share store">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg>
                                </button>
                            </div>
                            <div class="flex gap-4 mt-2 text-xs text-gray-500 font-bold">
                                <span><?php echo $productCount; ?> product<?php echo $productCount !== 1 ? 's' : ''; ?></span>
                                <span><?php echo (int) ($currentUser['token_balance'] ?? 0); ?> Vomp Coins</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>" class="px-5 py-2.5 rounded-xl bg-white/10 text-white text-sm font-black hover:bg-white/20 transition-all">View Storefront</a>
                        <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>" class="btn-press px-5 py-2.5 rounded-xl bg-[#ff610a] text-white text-sm font-black hover:bg-[#e05500] transition-all">Manage Store</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<script>
function shareStore(slug, name) {
    const url = window.location.origin + '/store/' + slug;
    if (navigator.share) {
        navigator.share({ title: name, url: url }).catch(() => {});
    } else {
        navigator.clipboard.writeText(url).then(() => {
            const btns = document.querySelectorAll('button[onclick*="' + slug + '"]');
            btns.forEach(btn => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
                setTimeout(() => btn.innerHTML = orig, 2000);
            });
        }).catch(() => {});
    }
}
</script>

<?php
$content = ob_get_clean();
?>
