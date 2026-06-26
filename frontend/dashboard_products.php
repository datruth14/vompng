<?php
/*
 * Dashboard product management template.
 */

$pageTitle = 'Manage Products - vomp';
ob_start();
?>
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet" />
<style>
.ts-wrapper .ts-control {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    color: #fff;
    font-size: 0.875rem;
    box-shadow: none;
}
.ts-wrapper .ts-control:hover { border-color: rgba(255,255,255,0.15); }
.ts-wrapper.focus .ts-control { border-color: rgba(255,97,10,0.5); box-shadow: none; }
.ts-wrapper .ts-control input { color: #ff610a; }
.ts-wrapper .ts-control .item { color: #fff; background: rgba(255,255,255,0.1); border-radius: 0.375rem; }
.ts-dropdown {
    background: #1a1a2e;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 0.75rem;
    color: #fff;
    z-index: 9999;
}
.ts-dropdown .option { color: #ccc; padding: 0.5rem 1rem; }
.ts-dropdown .option.active { background: rgba(255,97,10,0.2); color: #fff; }
.ts-dropdown .option.highlight { background: rgba(255,97,10,0.3); color: #fff; }
.ts-dropdown .no-results { color: #666; padding: 0.5rem 1rem; }
.ts-wrapper .ts-control .dropdown-active { border-color: rgba(255,97,10,0.5); }
</style>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate__animated animate__fadeInDown">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Inventory Management</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">Products</h1>
            <p class="text-gray-500 font-medium text-lg">Add or edit items for <?php echo htmlspecialchars($store['name']); ?>.</p>
        </div>
        <div class="flex flex-col items-end gap-3">
            <?php if ((int) ($currentUser['token_balance'] ?? 0) < 10): ?>
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 3h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                    <?php if ((int) ($currentUser['token_balance'] ?? 0) <= 0): ?>
                        No Vomp Coins &mdash; <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/tokens" class="underline">Top up to add products</a>
                    <?php else: ?>
                        Only <?php echo number_format((int) ($currentUser['token_balance'] ?? 0)); ?> Vomp Coin<?php echo (int) ($currentUser['token_balance'] ?? 0) !== 1 ? 's' : ''; ?> left &mdash; need 10 to publish. <a href="/dashboard/<?php echo htmlspecialchars($store['slug']); ?>/tokens" class="underline">Top up</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <span class="text-xs text-gray-500 font-bold"><?php echo number_format((int) ($currentUser['token_balance'] ?? 0)); ?> Vomp Coins remaining &bull; 10 Vomp Coins per product</span>
            <?php endif; ?>
            <button onclick="toggleAddForm()" <?php echo (int) ($currentUser['token_balance'] ?? 0) < 10 ? 'disabled title="Need at least 10 Vomp Coins"' : ''; ?> class="btn-press px-8 py-4 rounded-2xl bg-[#ff610a] text-white font-black text-sm shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all disabled:opacity-40 disabled:cursor-not-allowed">Add New Product</button>
        </div>
    </header>

    <!-- Add / Edit Product Form -->
    <div id="addProductForm" class="hidden glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10 mb-12 animate__animated animate__fadeInUp">
        <h2 id="formTitle" class="text-2xl font-black text-white mb-6">Create New Product</h2>
        <!-- Source Tabs -->
        <div class="flex gap-2 mb-8">
            <button type="button" id="tabStore" class="source-tab px-6 py-3 rounded-xl font-black text-sm transition-all bg-[#ff610a] text-white shadow-xl shadow-[#ff610a]/20" onclick="switchSource('store')">Add From My Store</button>
            <button type="button" id="tabAffiliate" class="source-tab px-6 py-3 rounded-xl font-black text-sm transition-all bg-white/5 text-gray-400 hover:bg-white/10" onclick="switchSource('affiliate')">Add From Affiliate Site</button>
        </div>
        <input type="hidden" id="pSource" value="store">
        <form id="productForm" class="grid grid-cols-1 md:grid-cols-2 gap-8" enctype="multipart/form-data">
            <input type="hidden" id="pId" value="">
            <div class="space-y-4">
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Product Name</label>
                    <input type="text" id="pName" required placeholder="e.g. Classic Sneakers" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                </div>
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Price (₦)</label>
                    <input type="text" id="pPrice" required placeholder="0.00" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                </div>
                <!-- My Store: file upload -->
                <div id="pMediaField">
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Product Image</label>
                    <input type="file" id="pMedia" accept="image/*" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-gray-400 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all file:bg-[#ff610a]/20 file:border-0 file:rounded-lg file:px-3 file:py-1 file:text-[#ff8c3a] file:font-bold file:text-xs file:cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG or WebP (compressed automatically)</p>
                </div>
                <!-- Affiliate: image URL + affiliate URL -->
                <div id="pAffiliateFields" class="hidden space-y-4">
                    <div>
                        <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Image URL</label>
                        <input type="url" id="pMediaUrl" placeholder="https://example.com/image.jpg" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                        <p class="text-xs text-gray-500 mt-1">Direct link to the product image</p>
                    </div>
                    <div>
                        <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Affiliate Product URL</label>
                        <input type="url" id="pAffiliateUrl" placeholder="https://affiliate-site.com/product/123" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                        <p class="text-xs text-gray-500 mt-1">Where buyers will be redirected to purchase</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Category</label>
                    <select id="pCategory" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                        <option value="" class="bg-gray-900 text-gray-400">Select a category</option>
                        <?php foreach ($productCategories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" class="bg-gray-900"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Country</label>
                    <select id="pCountry" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                        <option value="" class="bg-gray-900 text-gray-400">Select country</option>
                        <?php foreach ($countries as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>" class="bg-gray-900" <?php echo $c === 'Nigeria' ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">State / Region</label>
                    <input type="text" id="pState" placeholder="e.g. Lagos, Accra, Nairobi" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                </div>
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Currency</label>
                    <select id="pCurrency" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                        <option value="" class="bg-gray-900 text-gray-400">Select currency</option>
                        <?php foreach ($currencies as $code => $label): ?>
                            <option value="<?php echo htmlspecialchars($code); ?>" class="bg-gray-900" <?php echo $code === 'NGN' ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="field-label block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Description</label>
                    <textarea id="pDesc" rows="6" placeholder="Describe your product..." class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all"></textarea>
                </div>
                <div id="uploadProgressWrap" class="hidden w-full bg-white/5 rounded-full h-2 overflow-hidden">
                    <div id="uploadProgressBar" class="bg-[#ff610a] h-full rounded-full transition-all duration-300" style="width:0%"></div>
                </div>
                <div class="flex gap-4 justify-end pt-4">
                    <button type="button" onclick="toggleAddForm()" class="px-8 py-4 rounded-2xl bg-white/5 text-white font-black text-sm hover:bg-white/10 transition-all">Cancel</button>
                    <button type="submit" id="saveProductBtn" class="btn-press px-8 py-4 rounded-2xl bg-[#ff610a] text-white font-black text-sm shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">Save Product</button>
                </div>
                <div id="productFormMsg" class="mt-2"></div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate__animated animate__fadeInUp">
        <?php foreach ($products as $p): ?>
            <article class="glass-morphism rounded-[2rem] p-6 border border-white/10 flex flex-col group hover:bg-white/5 transition-all">
                <?php if ($p['media_url']): ?>
                    <div class="aspect-square rounded-2xl overflow-hidden mb-6 skeleton-box border border-white/5 relative">
                        <img src="<?php echo htmlspecialchars(img_url($p['media_url'])); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="img-skeleton w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')" onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center text-gray-600\'><svg class=\'w-10 h-10\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.5\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z\'/></svg></div>'">
                        <?php if (!empty($p['affiliate_url'])): ?>
                            <span class="absolute top-2 right-2 px-2 py-0.5 rounded-lg bg-purple-500/20 border border-purple-500/30 text-purple-300 text-[10px] font-black uppercase tracking-wider">Affiliate</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                        <h3 class="text-xl font-black text-white break-words"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <p class="text-[#ff610a] font-black whitespace-nowrap"><?php echo htmlspecialchars(product_get_currency_symbol($p['currency'] ?? 'NGN')); ?><?php echo number_format((float)$p['price'], 0); ?></p>
                    </div>
                    <p class="text-gray-500 text-sm line-clamp-2 mb-6"><?php echo htmlspecialchars($p['description']); ?></p>
                </div>

                <div class="flex items-center justify-between border-t border-white/5 pt-6">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full <?php echo (int)$p['is_available'] ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-rose-500'; ?>"></div>
                        <span class="text-xs font-black uppercase tracking-wider text-gray-400"><?php echo (int)$p['is_available'] ? 'Live' : 'Hidden'; ?></span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="editProduct(<?php echo htmlspecialchars(json_encode($p['id'])); ?>, <?php echo htmlspecialchars(json_encode($p['name'])); ?>, <?php echo htmlspecialchars(json_encode($p['price'])); ?>, <?php echo htmlspecialchars(json_encode($p['description'])); ?>, <?php echo htmlspecialchars(json_encode($p['country'] ?? 'Nigeria')); ?>, <?php echo htmlspecialchars(json_encode($p['state'] ?? '')); ?>, <?php echo htmlspecialchars(json_encode($p['currency'] ?? 'NGN')); ?>, <?php echo htmlspecialchars(json_encode($p['media_url'] ?? '')); ?>, <?php echo htmlspecialchars(json_encode($p['affiliate_url'] ?? '')); ?>)" class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                        </button>
                        <button onclick="deleteProduct('<?php echo $p['id']; ?>')" class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if (!$products): ?>
            <div class="col-span-full py-20 text-center glass-morphism rounded-[2.5rem] border border-dashed border-white/10 animate__animated animate__fadeInUp">
                <div class="w-20 h-20 rounded-3xl bg-white/5 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <h3 class="text-2xl font-black text-white mb-2">No products found</h3>
                <p class="text-gray-500 max-w-sm mx-auto">Your inventory is empty. Click "Add New Product" to start building your store.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
function formatNumber(n) {
    var parts = n.toString().split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return parts.join('.');
}

document.getElementById('pPrice').addEventListener('input', function () {
    var cursor = this.selectionStart;
    var raw = this.value.replace(/[^0-9.]/g, '');
    var dots = raw.match(/\./g);
    if (dots && dots.length > 1) raw = raw.replace(/\.+$/, '');
    if (raw) {
        this.value = formatNumber(raw);
        var diff = this.value.length - raw.length;
        this.setSelectionRange(cursor + diff, cursor + diff);
    }
});

let editingId = null;

function switchSource(source) {
    document.getElementById('pSource').value = source;
    document.getElementById('tabStore').className = 'source-tab px-6 py-3 rounded-xl font-black text-sm transition-all ' + (source === 'store' ? 'bg-[#ff610a] text-white shadow-xl shadow-[#ff610a]/20' : 'bg-white/5 text-gray-400 hover:bg-white/10');
    document.getElementById('tabAffiliate').className = 'source-tab px-6 py-3 rounded-xl font-black text-sm transition-all ' + (source === 'affiliate' ? 'bg-[#ff610a] text-white shadow-xl shadow-[#ff610a]/20' : 'bg-white/5 text-gray-400 hover:bg-white/10');
    document.getElementById('pMediaField').classList.toggle('hidden', source === 'affiliate');
    document.getElementById('pAffiliateFields').classList.toggle('hidden', source === 'store');
    document.getElementById('pMedia').required = (source === 'store');
}

function toggleAddForm() {
    const form = document.getElementById('addProductForm');
    form.classList.toggle('hidden');
    if (form.classList.contains('hidden')) {
        destroyFormTomSelects();
        resetForm();
    } else {
        initFormTomSelects();
        switchSource('store');
    }
}

function resetForm() {
    editingId = null;
    document.getElementById('pId').value = '';
    document.getElementById('pName').value = '';
    document.getElementById('pPrice').value = '';
    document.getElementById('pDesc').value = '';
    document.getElementById('pMedia').value = '';
    document.getElementById('pMediaUrl').value = '';
    document.getElementById('pAffiliateUrl').value = '';
    document.getElementById('productFormMsg').innerHTML = '';
    document.getElementById('formTitle').textContent = 'Create New Product';
    document.getElementById('pMediaField').classList.remove('hidden');
    document.getElementById('pAffiliateFields').classList.add('hidden');
    ['pCountry', 'pState', 'pCurrency'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el && el.tomselect) el.tomselect.setValue('');
    });
}

function editProduct(id, name, price, description, country, state, currency, mediaUrl, affiliateUrl) {
    editingId = id;
    document.getElementById('pId').value = id;
    document.getElementById('pName').value = name;
    document.getElementById('pPrice').value = formatNumber(price.toString().replace(/[^0-9.]/g, ''));
    document.getElementById('pDesc').value = description;
    document.getElementById('pMedia').value = '';
    document.getElementById('pMediaUrl').value = mediaUrl || '';
    document.getElementById('pAffiliateUrl').value = affiliateUrl || '';
    document.getElementById('productFormMsg').innerHTML = '';
    document.getElementById('formTitle').textContent = 'Edit Product';

    var isAffiliate = affiliateUrl && affiliateUrl.length > 0;
    switchSource(isAffiliate ? 'affiliate' : 'store');
    if (isAffiliate) {
        document.getElementById('pMediaField').classList.add('hidden');
    }

    const form = document.getElementById('addProductForm');
    form.classList.remove('hidden');
    initFormTomSelects();

    ['pCountry', 'pState', 'pCurrency'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            var val = id === 'pCountry' ? (country || 'Nigeria') : id === 'pState' ? (state || '') : (currency || 'NGN');
            if (el.tomselect) { el.tomselect.setValue(val); }
            else { el.value = val; }
        }
    });

    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* Country data mapping: name -> {alpha2, currency} from API */
const COUNTRY_DATA = <?php echo json_encode(!empty($countryData) ? array_combine(array_map(fn($c) => $c['name'], $countryData), $countryData) : [], JSON_UNESCAPED_UNICODE); ?>;

/* Init form TomSelects (called when form is shown) */
var formTomSelects = [];
function initFormTomSelects() {
    destroyFormTomSelects();
    var countryTs = new TomSelect('#pCountry', {
        placeholder: 'Search country...',
        allowEmptyOption: true,
        maxItems: 1,
        onChange: function (value) {
            var country = COUNTRY_DATA[value];
            var currencyTs = document.getElementById('pCurrency').tomselect;
            if (country && country.currencyCode && currencyTs) {
                currencyTs.setValue(country.currencyCode);
            }
        }
    });
    formTomSelects.push(countryTs);

    var currencyTs = new TomSelect('#pCurrency', {
        placeholder: 'Search currency...',
        allowEmptyOption: true,
        maxItems: 1,
    });
    formTomSelects.push(currencyTs);
}

function destroyFormTomSelects() {
    formTomSelects.forEach(function (ts) { ts.destroy(); });
    formTomSelects = [];
}

function compressImage(file, quality = 0.7, maxDim = 1600) {
    return new Promise((resolve) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            let w = img.width, h = img.height;
            if (w > maxDim || h > maxDim) {
                const r = Math.min(maxDim / w, maxDim / h);
                w = Math.round(w * r);
                h = Math.round(h * r);
            }
            const c = document.createElement('canvas');
            c.width = w;
            c.height = h;
            const ctx = c.getContext('2d');
            ctx.drawImage(img, 0, 0, w, h);
            const supportsWebp = c.toDataURL('image/webp').indexOf('image/webp') === 5;
            const mime = supportsWebp ? 'image/webp' : 'image/jpeg';
            const ext = supportsWebp ? '.webp' : '.jpg';
            c.toBlob((blob) => {
                if (blob) {
                    const name = file.name.replace(/\.[^.]+$/, ext);
                    resolve(new File([blob], name, { type: mime }));
                } else {
                    resolve(file);
                }
            }, mime, quality);
        };
        img.onerror = () => { URL.revokeObjectURL(url); resolve(file); };
        img.src = url;
    });
}

document.getElementById('productForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveProductBtn');
    const progressWrap = document.getElementById('uploadProgressWrap');
    const progressBar = document.getElementById('uploadProgressBar');
    const msgEl = document.getElementById('productFormMsg');

    btn.disabled = true;
    btn.textContent = 'Compressing...';
    msgEl.innerHTML = '';
    progressWrap.classList.remove('hidden');
    progressBar.style.width = '0%';

    const formData = new FormData();
    formData.append('name', document.getElementById('pName').value);
    formData.append('price', document.getElementById('pPrice').value.replace(/,/g, ''));
    formData.append('description', document.getElementById('pDesc').value);
    formData.append('category', document.getElementById('pCategory').value);
    formData.append('country', document.getElementById('pCountry').value || 'Nigeria');
    formData.append('state', document.getElementById('pState').value);
    formData.append('currency', document.getElementById('pCurrency').value || 'NGN');

    var source = document.getElementById('pSource').value;
    if (source === 'affiliate') {
        formData.append('media_url', document.getElementById('pMediaUrl').value);
        formData.append('affiliate_url', document.getElementById('pAffiliateUrl').value);
    }

    const fileInput = document.getElementById('pMedia');
    if (fileInput.files.length > 0) {
        btn.textContent = 'Compressing...';
        const compressed = await compressImage(fileInput.files[0]);
        formData.append('media', compressed);
    }

    const slug = '<?php echo $store['slug']; ?>';
    const action = editingId ? 'update' : 'create';
    let url = `/api/products.php?storeSlug=${slug}&action=${action}`;
    if (editingId) {
        url += `&id=${editingId}`;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', url);

    xhr.upload.onprogress = (evt) => {
        if (evt.lengthComputable) {
            const pct = Math.round((evt.loaded / evt.total) * 100);
            progressBar.style.width = pct + '%';
            btn.textContent = pct < 100 ? `Uploading ${pct}%` : 'Saving...';
        }
    };

    xhr.onload = () => {
        progressWrap.classList.add('hidden');
        try {
            const result = JSON.parse(xhr.responseText);
            if (result.success) {
                location.reload();
            } else if (result.code === 'NO_TOKENS') {
                msgEl.innerHTML = `<div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 3h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                    No Vomp Coins left. <a href="/dashboard/${slug}/tokens" class="underline ml-1">Top up your balance</a> to publish more products.
                </div>`;
                btn.disabled = false;
                btn.textContent = 'Save Product';
            } else {
                alert(result.error || 'Failed to save product');
                btn.disabled = false;
                btn.textContent = 'Save Product';
            }
        } catch (err) {
            alert('Server error. Check console.');
            btn.disabled = false;
            btn.textContent = 'Save Product';
        }
    };

    xhr.onerror = () => {
        progressWrap.classList.add('hidden');
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Save Product';
    };

    xhr.send(formData);
});

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;

    const slug = '<?php echo $store['slug']; ?>';
    try {
        const res = await fetch(`/api/products.php?storeSlug=${slug}&action=delete&id=${id}`, {
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
