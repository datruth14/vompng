<?php
$pageTitle = htmlspecialchars($store['name']) . ' - Storefront';
ob_start();
?>
<section class="py-8 space-y-10">
    <header class="glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 overflow-hidden relative">
        <div class="absolute inset-0 opacity-20" style="background: radial-gradient(circle at top right, <?php echo htmlspecialchars($store['accent_color'] ?? '#8b5cf6'); ?>, transparent 45%), radial-gradient(circle at bottom left, <?php echo htmlspecialchars($store['hero_color'] ?? '#4f46e5'); ?>, transparent 45%);"></div>
        <div class="relative z-10">
            <p class="text-xs uppercase tracking-[0.2em] font-black text-indigo-300 mb-2">Storefront</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-3"><?php echo htmlspecialchars($store['name']); ?></h1>
            <p class="text-gray-300 max-w-2xl"><?php echo htmlspecialchars($store['description'] ?: 'Browse products and order directly via WhatsApp.'); ?></p>
            <p class="mt-4 text-sm text-gray-400">Available order tokens: <span class="font-black text-white"><?php echo (int) $store['token_balance']; ?></span></p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($products as $product): ?>
            <article class="glass-morphism rounded-3xl p-5 border border-white/10 flex flex-col gap-4">
                <?php if (!empty($product['media_url'])): ?>
                    <div class="aspect-video rounded-2xl overflow-hidden bg-white/5">
                        <img src="<?php echo htmlspecialchars($product['media_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>
                <div>
                    <h3 class="text-2xl font-black text-white"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="text-gray-400 text-sm mt-2"><?php echo htmlspecialchars($product['description'] ?: ''); ?></p>
                </div>
                <div class="mt-auto flex items-center justify-between gap-3">
                    <p class="text-indigo-300 font-black text-xl">₦<?php echo number_format((float) $product['price'], 2); ?></p>
                    <button class="btn-primary px-5 py-2.5 rounded-xl order-btn" data-store="<?php echo htmlspecialchars($store['slug']); ?>">Order via WhatsApp</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (!$products): ?>
        <div class="glass-morphism rounded-3xl p-10 border border-white/10 text-center">
            <h3 class="text-2xl font-black text-white mb-2">No products yet</h3>
            <p class="text-gray-400">This seller has not published any product right now.</p>
        </div>
    <?php endif; ?>
</section>

<script>
(() => {
  const buttons = document.querySelectorAll('.order-btn');
  buttons.forEach((btn) => {
    btn.addEventListener('click', async () => {
      btn.disabled = true;
      const old = btn.textContent;
      btn.textContent = 'Processing...';
      try {
        const res = await fetch('/api/tokens/deduct?storeSlug=' + encodeURIComponent(btn.dataset.store), { headers: { Accept: 'application/json' }});
        const data = await res.json();
        if (!res.ok) {
          alert(data.error || 'Could not continue to WhatsApp');
          btn.disabled = false;
          btn.textContent = old;
          return;
        }
        window.location.href = data.whatsappUrl;
      } catch (e) {
        alert('Something went wrong. Try again.');
        btn.disabled = false;
        btn.textContent = old;
      }
    });
  });
})();
</script>
<?php
$content = ob_get_clean();
?>