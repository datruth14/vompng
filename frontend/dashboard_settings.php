<?php
$pageTitle = 'Store Settings - VomP';
ob_start();
?>
<section class="py-6 md:py-10">
    <header class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-indigo-400 mb-2">Editing <?php echo htmlspecialchars($store['slug']); ?></p>
            <h1 class="text-4xl font-black text-white tracking-tight mb-1">Store Settings</h1>
            <p class="text-gray-400 text-sm">Update your storefront details and contact info.</p>
        </div>
        <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>" target="_blank" class="btn-secondary px-6 py-3 rounded-2xl">Open Storefront</a>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <form id="settingsForm" class="glass-morphism rounded-2xl p-6 border border-white/10 md:col-span-2 space-y-4">
            <div>
                <label class="text-sm text-gray-300 font-bold">Store Name</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($store['name']); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-indigo-400" />
            </div>

            <div>
                <label class="text-sm text-gray-300 font-bold">Description</label>
                <textarea name="description" id="description" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-indigo-400"><?php echo htmlspecialchars($store['description']); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-300 font-bold">Contact Phone</label>
                    <input type="text" name="contact_phone" id="contact_phone" value="<?php echo htmlspecialchars($store['contact_phone'] ?? ''); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5" />
                </div>
                <div>
                    <label class="text-sm text-gray-300 font-bold">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" value="<?php echo htmlspecialchars($store['contact_email'] ?? ''); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5" />
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-300 font-bold">Logo URL</label>
                <input type="text" name="logo_url" id="logo_url" value="<?php echo htmlspecialchars($store['logo_url'] ?? ''); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5" />
            </div>

            <div>
                <label class="text-sm text-gray-300 font-bold">Hero Image URL</label>
                <input type="text" name="hero_image_url" id="hero_image_url" value="<?php echo htmlspecialchars($store['hero_image_url'] ?? ''); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-300 font-bold">Accent Color</label>
                    <input type="color" name="accent_color" id="accent_color" value="<?php echo htmlspecialchars($store['accent_color'] ?? '#6b21a8'); ?>" class="mt-2 w-full rounded-xl" />
                </div>
                <div>
                    <label class="text-sm text-gray-300 font-bold">Hero Color</label>
                    <input type="color" name="hero_color" id="hero_color" value="<?php echo htmlspecialchars($store['hero_color'] ?? '#4f46e5'); ?>" class="mt-2 w-full rounded-xl" />
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" <?php echo (isset($store['is_active']) && (int)$store['is_active'] === 1) ? 'checked' : ''; ?> />
                <label for="is_active" class="text-sm text-gray-300 font-bold">Store is active (public)</label>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" id="saveBtn" class="btn-press px-6 py-3 rounded-2xl bg-indigo-500 text-white font-black">Save</button>
            </div>

            <div id="msg" class="mt-3"></div>
        </form>

        <aside class="glass-morphism rounded-2xl p-6 border border-white/10">
            <h3 class="text-white font-black text-xl mb-3">Preview</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <?php if (!empty($store['logo_url'])): ?>
                        <img src="<?php echo htmlspecialchars($store['logo_url']); ?>" alt="logo" class="w-16 h-16 rounded-md object-cover" />
                    <?php else: ?>
                        <div class="w-16 h-16 rounded-md bg-white/5 flex items-center justify-center text-2xl font-black">V</div>
                    <?php endif; ?>
                    <div>
                        <div class="text-lg font-black text-white"><?php echo htmlspecialchars($store['name']); ?></div>
                        <div class="text-sm text-gray-400"><?php echo htmlspecialchars($store['contact_phone'] ?? ''); ?></div>
                    </div>
                </div>
                <?php if (!empty($store['hero_image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($store['hero_image_url']); ?>" class="w-full rounded-xl object-cover" />
                <?php endif; ?>
                <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($store['description'] ?? ''); ?></p>
            </div>
        </aside>
    </div>

</section>

<script>
document.getElementById('saveBtn').addEventListener('click', async function() {
    const data = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value,
        contact_phone: document.getElementById('contact_phone').value,
        contact_email: document.getElementById('contact_email').value,
        logo_url: document.getElementById('logo_url').value,
        hero_image_url: document.getElementById('hero_image_url').value,
        accent_color: document.getElementById('accent_color').value,
        hero_color: document.getElementById('hero_color').value,
        is_active: document.getElementById('is_active').checked ? 1 : 0,
    };

    const slug = '<?php echo htmlspecialchars($store['slug']); ?>';

    const res = await fetch('/api/settings?storeSlug=' + encodeURIComponent(slug), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });

    const json = await res.json();
    const msg = document.getElementById('msg');
    if (json.success) {
        msg.innerHTML = '<div class="px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 font-bold">Saved successfully</div>';
        setTimeout(() => { location.reload(); }, 900);
    } else {
        msg.innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">' + (json.error || 'Failed to save') + '</div>';
    }
});
</script>

<?php
$content = ob_get_clean();
?>