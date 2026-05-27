<?php
$pageTitle = 'All Stores - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">All Stores</h1>
            <p class="text-gray-500 font-medium text-lg">Browse stores on the marketplace.</p>
        </div>
    </header>

    <form method="GET" action="/stores" class="relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        <input type="text" name="q" value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>" placeholder="Search stores..." class="w-full rounded-xl px-5 py-3.5 pl-12 bg-transparent border border-white/10 focus:border-[#ff610a] focus:outline-none text-white placeholder-gray-500 transition-colors" />
    </form>

    <?php if ($searchQuery): ?>
        <p class="text-sm text-gray-400"><?php echo $totalStores; ?> result<?php echo $totalStores !== 1 ? 's' : ''; ?> for "<span class="text-white font-bold"><?php echo htmlspecialchars($searchQuery); ?></span>"</p>
    <?php elseif ($totalStores > $perPage): ?>
        <p class="text-sm text-gray-400">Page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo $totalStores; ?> stores)</p>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($stores as $s):
            $hasHero = !empty($s['hero_image_url']);
        ?>
            <a href="/store/<?php echo htmlspecialchars($s['slug']); ?>" class="rounded-3xl p-6 border border-white/10 hover:border-[#ff610a]/30 transition-all group block relative overflow-hidden min-h-[200px] <?php echo $hasHero ? '' : 'glass-morphism'; ?>">
                <?php if ($hasHero): ?>
                <div class="absolute inset-0 skeleton-box overflow-hidden">
                    <img src="<?php echo htmlspecialchars(img_url($s['hero_image_url'])); ?>" alt="" class="img-skeleton w-full h-full object-cover" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')" />
                </div>
                <?php endif; ?>
                <div class="absolute inset-0 <?php echo $hasHero ? 'z-10 bg-gradient-to-t from-black/80 via-black/40 to-transparent' : 'bg-gradient-to-t from-black/60 via-black/20 to-transparent'; ?>"></div>
                <div class="relative <?php echo $hasHero ? 'z-20' : 'z-10'; ?> flex flex-col h-full min-h-[170px]">
                    <div class="w-14 h-14 rounded-2xl bg-[#ff610a]/20 backdrop-blur-sm flex items-center justify-center text-xl font-black text-[#ff610a] mb-3 <?php echo $hasHero ? '' : ''; ?>">
                        <?php echo strtoupper($s['name'][0]); ?>
                    </div>
                    <div class="mt-auto">
                        <h3 class="text-xl font-black text-white group-hover:text-[#ff610a] transition-colors mb-1"><?php echo htmlspecialchars($s['name']); ?></h3>
                        <?php if ($s['description']): ?>
                            <p class="text-sm <?php echo $hasHero ? 'text-gray-300' : 'text-gray-400'; ?> line-clamp-2"><?php echo htmlspecialchars($s['description']); ?></p>
                        <?php endif; ?>
                        <div class="flex items-center gap-2 mt-4 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                            <span>Visit Store →</span>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($stores)): ?>
        <div class="py-16 text-center">
            <p class="text-gray-500 font-medium text-lg">No stores yet.</p>
        </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-2 pt-4">
            <?php if ($page > 1): ?>
                <a href="/stores?<?php echo $searchQuery ? 'q=' . urlencode($searchQuery) . '&' : ''; ?>page=<?php echo $page - 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="/stores?<?php echo $searchQuery ? 'q=' . urlencode($searchQuery) . '&' : ''; ?>page=<?php echo $i; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="/stores?<?php echo $searchQuery ? 'q=' . urlencode($searchQuery) . '&' : ''; ?>page=<?php echo $page + 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<footer class="py-16 md:py-20 border-t border-white/5 mt-16">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">
        <div class="md:col-span-2">
            <img src="/assets/img/logo.png" alt="vomp" class="h-10 w-auto mb-4">
            <p class="text-gray-400 text-sm leading-relaxed max-w-sm">vomp is Nigeria's simplest marketplace platform. Create your store, list products, and receive orders directly via WhatsApp — no technical skills required.</p>
            <div class="flex items-center gap-3 mt-5">
                <a href="mailto:support@vomp.ng" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                </a>
                <a href="https://wa.me/2349115963439" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-wider mb-4">Quick Links</h4>
            <ul class="space-y-3">
                <li><a href="/marketplace" class="text-gray-400 hover:text-white text-sm transition-colors">Marketplace</a></li>
                <?php if ($currentUser): ?>
                    <li><a href="/dashboard" class="text-gray-400 hover:text-white text-sm transition-colors">Dashboard</a></li>
                    <li><a href="/logout" class="text-gray-400 hover:text-white text-sm transition-colors">Logout</a></li>
                <?php else: ?>
                    <li><a href="/register" class="text-gray-400 hover:text-white text-sm transition-colors">Create a Store</a></li>
                    <li><a href="/login" class="text-gray-400 hover:text-white text-sm transition-colors">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-wider mb-4">Contact</h4>
            <ul class="space-y-3 text-sm text-gray-400">
                <li>support@vomp.ng</li>
                <li>(234) 9115 963 439</li>
                <li>Mowe, Ogun State, Nigeria.</li>
            </ul>
        </div>
    </div>
    <div class="max-w-6xl mx-auto mt-10 pt-6 border-t border-white/5 text-center text-xs text-gray-600">
        &copy; <?php echo date('Y'); ?> vomp. All rights reserved.
    </div>
    <div class="max-w-6xl mx-auto mt-3 text-center text-xs text-gray-700">
        vomp is a product of 14Eter Limited RC: 1865845
    </div>
</footer>

<?php
$content = ob_get_clean();
?>
