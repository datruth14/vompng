<?php
$pageTitle = 'Manage Products - VomP';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-indigo-400 mb-2">Inventory Management</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">Products</h1>
            <p class="text-gray-500 font-medium text-lg">Add or edit items for <?php echo htmlspecialchars($store['name']); ?>.</p>
        </div>
        <button onclick="toggleAddForm()" class="btn-press px-8 py-4 rounded-2xl bg-indigo-500 text-white font-black text-sm shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all">Add New Product</button>
    </header>

    <!-- Add Product Form (Initially Hidden) -->
    <div id="addProductForm" class="hidden glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10 mb-12">
        <h2 class="text-2xl font-black text-white mb-6">Create New Product</h2>
        <form id="productForm" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Product Name</label>
                    <input type="text" id="pName" required placeholder="e.g. Classic Sneakers" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all">
                </div>
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Price (₦)</label>
                    <input type="number" id="pPrice" required placeholder="0.00" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all">
                </div>
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Image URL</label>
                    <input type="text" id="pMedia" placeholder="https://..." class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all">
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Description</label>
                    <textarea id="pDesc" rows="6" placeholder="Describe your product..." class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all"></textarea>
                </div>
                <div class="flex gap-4 justify-end pt-4">
                    <button type="button" onclick="toggleAddForm()" class="px-8 py-4 rounded-2xl bg-white/5 text-white font-black text-sm hover:bg-white/10 transition-all">Cancel</button>
                    <button type="submit" class="btn-press px-8 py-4 rounded-2xl bg-indigo-500 text-white font-black text-sm shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all">Save Product</button>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($products as $p): ?>
            <article class="glass-morphism rounded-[2rem] p-6 border border-white/10 flex flex-col group hover:bg-white/5 transition-all">
                <?php if ($p['media_url']): ?>
                    <div class="aspect-square rounded-2xl overflow-hidden mb-6 bg-white/5 border border-white/5">
                        <img src="<?php echo htmlspecialchars($p['media_url']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                <?php endif; ?>
                
                <div class="flex-1">
                    <div class="flex items-start justify-between gap-4 mb-2">
                        <h3 class="text-xl font-black text-white"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <p class="text-indigo-400 font-black">₦<?php echo number_format((float)$p['price'], 0); ?></p>
                    </div>
                    <p class="text-gray-500 text-sm line-clamp-2 mb-6"><?php echo htmlspecialchars($p['description']); ?></p>
                </div>

                <div class="flex items-center justify-between border-t border-white/5 pt-6">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full <?php echo (int)$p['is_available'] ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-rose-500'; ?>"></div>
                        <span class="text-xs font-black uppercase tracking-wider text-gray-400"><?php echo (int)$p['is_available'] ? 'Live' : 'Hidden'; ?></span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="deleteProduct('<?php echo $p['id']; ?>')" class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if (!$products): ?>
            <div class="col-span-full py-20 text-center glass-morphism rounded-[2.5rem] border border-dashed border-white/10">
                <div class="w-20 h-20 rounded-3xl bg-white/5 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <h3 class="text-2xl font-black text-white mb-2">No products found</h3>
                <p class="text-gray-500 max-w-sm mx-auto">Your inventory is empty. Click "Add New Product" to start building your store.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function toggleAddForm() {
    const form = document.getElementById('addProductForm');
    form.classList.toggle('hidden');
}

document.getElementById('productForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const data = {
        name: document.getElementById('pName').value,
        price: document.getElementById('pPrice').value,
        media_url: document.getElementById('pMedia').value,
        description: document.getElementById('pDesc').value
    };

    const slug = '<?php echo $store['slug']; ?>';
    try {
        const res = await fetch(`/api/products?storeSlug=${slug}&action=create`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            location.reload();
        } else {
            alert(result.error || 'Failed to save product');
            btn.disabled = false;
            btn.textContent = 'Save Product';
        }
    } catch (err) {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Save Product';
    }
});

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    
    const slug = '<?php echo $store['slug']; ?>';
    try {
        const res = await fetch(`/api/products?storeSlug=${slug}&action=delete&id=${id}`, {
            method: 'POST'
        });
        const result = await res.json();
        if (result.success) {
            location.reload();
        } else {
            alert(result.error || 'Failed to delete product');
        }
    } catch (err) {
        alert('Network error. Please try again.');
    }
}
</script>

<?php
$content = ob_get_clean();
?>
