<?php
/*
 * Product detail page for a public storefront.
 */

$pageTitle = htmlspecialchars($product['name']) . ' - ' . htmlspecialchars($store['name']);
ob_start();
?>
<section class="py-10 md:py-16">
    <div class="max-w-5xl mx-auto space-y-10">
        <div class="glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
                <div class="rounded-3xl overflow-hidden skeleton-box border border-white/10 flex items-center justify-center">
                    <?php
                    $imgUrl = $product['media_url'];
                    if ($imgUrl && $imgUrl[0] !== '/') {
                        $imgUrl = '/' . $imgUrl;
                    }
                    ?>
                    <?php if (!empty($product['media_url'])): ?>
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-skeleton w-full max-h-[500px] object-contain" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')" onerror="this.parentElement.innerHTML='<div class=\'h-64 flex items-center justify-center text-gray-500 text-sm\'>Image not available</div>'" />
                    <?php else: ?>
                        <div class="h-64 flex items-center justify-center text-gray-500 text-sm">No image available</div>
                    <?php endif; ?>
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Product Details</p>
                            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight break-words"><?php echo htmlspecialchars($product['name']); ?></h1>
                        </div>
                        <span class="text-3xl font-black text-[#ff8c3a] whitespace-nowrap">₦<?php echo number_format((float) $product['price'], 2); ?></span>
                    </div>

                    <div class="text-gray-400 text-base leading-7">
                        <?php echo nl2br(htmlspecialchars($product['description'] ?: 'No description provided.')); ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="glass-morphism rounded-3xl p-6 border border-white/10">
                            <p class="text-xs uppercase tracking-[0.2em] font-black text-gray-400 mb-3">Store</p>
                            <p class="text-white font-bold"><?php echo htmlspecialchars($store['name']); ?></p>
                            <p class="text-gray-400 text-sm mt-2"><?php echo htmlspecialchars($store['description'] ?: 'No store description'); ?></p>
                        </div>
                        <div class="glass-morphism rounded-3xl p-6 border border-white/10">
                            <p class="text-xs uppercase tracking-[0.2em] font-black text-gray-400 mb-3">Order</p>
                            <p class="text-white font-bold">WhatsApp order</p>
                            <p class="text-gray-400 text-sm mt-2">Place an order directly with the seller via WhatsApp.</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button id="order-now" class="px-8 py-4 rounded-3xl bg-[#ff610a] text-white font-black text-base shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">Order via WhatsApp</button>
                        <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>" class="px-8 py-4 rounded-3xl bg-white/5 border border-white/10 text-white font-black text-base text-center hover:bg-white/10 transition-all">Back to storefront</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Order Form Modal -->
<div id="orderModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeOrderModal()"></div>
    <div class="relative glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <button onclick="closeOrderModal()" class="absolute top-4 right-4 p-2 rounded-xl bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <h2 class="text-2xl font-black text-white mb-2">Place Your Order</h2>
        <p class="text-gray-400 text-sm mb-6">Fill in your details below. Your order will be sent directly to the seller via WhatsApp.</p>
        <form id="orderForm" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Full Name</label>
                <input type="text" id="oName" required placeholder="e.g. John Doe" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Email Address</label>
                <input type="email" id="oEmail" required placeholder="you@example.com" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">State</label>
                <input type="text" id="oState" required placeholder="e.g. Lagos" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Delivery Location</label>
                <textarea id="oDelivery" required rows="2" placeholder="e.g. 123 Main Street, Ikeja" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all"></textarea>
            </div>
            <button type="submit" id="orderSubmit" class="btn-press w-full py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">
                Send Order via WhatsApp
            </button>
            <div id="orderFormMsg" class="mt-2"></div>
        </form>
    </div>
</div>

<script>
function openOrderModal() {
    document.getElementById('orderModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('order-now').addEventListener('click', openOrderModal);

document.getElementById('orderForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('orderSubmit');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    const payload = {
        name: document.getElementById('oName').value.trim(),
        email: document.getElementById('oEmail').value.trim(),
        state: document.getElementById('oState').value.trim(),
        delivery_location: document.getElementById('oDelivery').value.trim(),
    };

    try {
        const res = await fetch('/api/tokens_deduct.php?storeSlug=<?php echo urlencode($store['slug']); ?>&productId=<?php echo urlencode($product['id']); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.whatsappUrl) {
            document.getElementById('orderFormMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">' + (data.error || 'Unable to generate WhatsApp link.') + '</div>';
            btn.disabled = false;
            btn.textContent = 'Send Order via WhatsApp';
            return;
        }
        window.location.href = data.whatsappUrl;
    } catch (err) {
        document.getElementById('orderFormMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Network error. Please try again.</div>';
        btn.disabled = false;
        btn.textContent = 'Send Order via WhatsApp';
    }
});
</script>
<?php
$content = ob_get_clean();
?>