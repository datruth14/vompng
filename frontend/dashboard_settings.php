<?php
/*
 * Dashboard settings template for store configuration.
 */

$pageTitle = 'Store Settings - vomp';
ob_start();
?>
<section class="py-6 md:py-10">
    <header class="flex items-center justify-between mb-6 animate__animated animate__fadeInDown">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Editing <?php echo htmlspecialchars($store['slug']); ?></p>
            <h1 class="text-4xl font-black text-white tracking-tight mb-1">Store Settings</h1>
            <p class="text-gray-400 text-sm">Update your storefront details and contact info.</p>
        </div>
        <a href="/store/<?php echo htmlspecialchars($store['slug']); ?>" target="_blank" class="btn-secondary px-6 py-3 rounded-2xl">Open Storefront</a>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <form id="settingsForm" class="glass-morphism rounded-2xl p-6 border border-white/10 md:col-span-2 space-y-4 animate__animated animate__fadeInUp">
            <div>
                <label class="text-sm text-gray-300 font-bold">Store Name</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($store['name']); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a]" />
            </div>

            <div>
                <label class="text-sm text-gray-300 font-bold">Description</label>
                <textarea name="description" id="description" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a]"><?php echo htmlspecialchars($store['description']); ?></textarea>
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

            <hr class="border-white/10">

            <div>
                <label class="text-sm text-gray-300 font-bold">Hero Image</label>
                <input type="file" name="hero_image" id="hero_image" accept="image/*" class="mt-2 w-full rounded-xl px-4 py-3 bg-white/5 border border-white/5 text-gray-400 focus:outline-none focus:border-[#ff610a]/50 file:bg-[#ff610a]/20 file:border-0 file:rounded-lg file:px-3 file:py-1 file:text-[#ff8c3a] file:font-bold file:text-xs file:cursor-pointer" />
                <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF or WebP (Max 5MB). Leave blank to keep existing image.</p>
            </div>

            <hr class="border-white/10">

            <div>
                <p class="text-sm text-gray-300 font-bold mb-3">Social Media Handles</p>
                <p class="text-xs text-gray-500 mb-3">Enter full URLs (e.g. https://instagram.com/yourhandle)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-400 font-semibold">Facebook</label>
                        <input type="url" name="social_facebook" id="social_facebook" value="<?php echo htmlspecialchars($store['social_facebook'] ?? ''); ?>" placeholder="https://facebook.com/..." class="mt-1 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a]" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 font-semibold">Instagram</label>
                        <input type="url" name="social_instagram" id="social_instagram" value="<?php echo htmlspecialchars($store['social_instagram'] ?? ''); ?>" placeholder="https://instagram.com/..." class="mt-1 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a]" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 font-semibold">Twitter / X</label>
                        <input type="url" name="social_twitter" id="social_twitter" value="<?php echo htmlspecialchars($store['social_twitter'] ?? ''); ?>" placeholder="https://twitter.com/..." class="mt-1 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a]" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-400 font-semibold">TikTok</label>
                        <input type="url" name="social_tiktok" id="social_tiktok" value="<?php echo htmlspecialchars($store['social_tiktok'] ?? ''); ?>" placeholder="https://tiktok.com/..." class="mt-1 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a]" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-400 font-semibold">YouTube</label>
                        <input type="url" name="social_youtube" id="social_youtube" value="<?php echo htmlspecialchars($store['social_youtube'] ?? ''); ?>" placeholder="https://youtube.com/..." class="mt-1 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a]" />
                    </div>
                </div>
            </div>

            <hr class="border-white/10">

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" <?php echo (isset($store['is_active']) && (int)$store['is_active'] === 1) ? 'checked' : ''; ?> />
                <label for="is_active" class="text-sm text-gray-300 font-bold">Store is active (public)</label>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" id="saveBtn" class="btn-press px-6 py-3 rounded-2xl bg-[#ff610a] text-white font-black">Save</button>
            </div>

            <div id="msg" class="mt-3"></div>
        </form>

        <aside class="glass-morphism rounded-2xl p-6 border border-white/10 animate__animated animate__fadeInUp">
            <h3 class="text-white font-black text-xl mb-3">Preview</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#ff610a] to-purple-600 flex items-center justify-center shadow-lg shadow-[#ff610a]/20 text-white font-black text-2xl">
                        <?php echo strtoupper(substr(htmlspecialchars($store['name']), 0, 1)); ?>
                    </div>
                    <div>
                        <div class="text-lg font-black text-white"><?php echo htmlspecialchars($store['name']); ?></div>
                        <div class="text-sm text-gray-400"><?php echo htmlspecialchars($store['contact_phone'] ?? ''); ?></div>
                    </div>
                </div>
                <?php if (!empty($store['hero_image_url'])): ?>
                    <div class="w-full rounded-xl overflow-hidden skeleton-box">
                        <img src="<?php echo htmlspecialchars(img_url($store['hero_image_url'])); ?>" class="img-skeleton w-full object-cover" onload="this.parentElement.classList.remove('skeleton-box');this.classList.add('loaded')" />
                    </div>
                <?php endif; ?>
                <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($store['description'] ?? ''); ?></p>
            </div>
        </aside>
    </div>

</section>

<script>
document.getElementById('saveBtn').addEventListener('click', async function() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const formData = new FormData();
    formData.append('name', document.getElementById('name').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('contact_phone', document.getElementById('contact_phone').value);
    formData.append('contact_email', document.getElementById('contact_email').value);
    formData.append('social_facebook', document.getElementById('social_facebook').value);
    formData.append('social_instagram', document.getElementById('social_instagram').value);
    formData.append('social_twitter', document.getElementById('social_twitter').value);
    formData.append('social_tiktok', document.getElementById('social_tiktok').value);
    formData.append('social_youtube', document.getElementById('social_youtube').value);
    formData.append('is_active', document.getElementById('is_active').checked ? 1 : 0);

    const fileInput = document.getElementById('hero_image');
    if (fileInput.files.length > 0) {
        formData.append('hero_image', fileInput.files[0]);
    }

    const slug = '<?php echo htmlspecialchars($store['slug']); ?>';

    try {
        const res = await fetch('/api/settings.php?storeSlug=' + encodeURIComponent(slug), {
            method: 'POST',
            body: formData
        });

        const json = await res.json();
        const msg = document.getElementById('msg');
        if (json.success) {
            msg.innerHTML = '<div class="px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 font-bold">Saved successfully</div>';
            setTimeout(() => { location.reload(); }, 900);
        } else {
            msg.innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">' + (json.error || 'Failed to save') + '</div>';
            btn.disabled = false;
            btn.textContent = 'Save';
        }
    } catch (err) {
        document.getElementById('msg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">Network error. Please try again.</div>';
        btn.disabled = false;
        btn.textContent = 'Save';
    }
});
</script>

<?php
$content = ob_get_clean();
?>