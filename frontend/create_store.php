<?php
$pageTitle = 'Create Store - vomp';
ob_start();
?>
<section class="min-h-[78vh] flex items-center justify-center py-10">
    <div class="w-full max-w-xl glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 bg-[#ff610a]/10 blur-[80px] rounded-full"></div>
        <div class="relative z-10">
            <h2 class="text-4xl font-black text-white mb-2 tracking-tight">New Store</h2>
            <p class="text-gray-400 font-medium mb-8">Create another store under your account.</p>

            <div id="error-box" class="hidden px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold mb-6"></div>

            <form id="create-store-form" class="space-y-6">
                <label class="field-label">Store Name</label>
                <input id="storeName" name="storeName" class="field-input" placeholder="e.g. My Second Store" required>

                <div class="p-3 rounded-xl bg-[#ff610a]/5 border border-[#ff610a]/10">
                    <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Preview URL</p>
                    <p id="preview-url" class="text-[#ff610a] font-mono text-xs truncate">/store/your-store</p>
                </div>

                <label class="field-label mt-5">Store Bio</label>
                <textarea id="storeDescription" name="storeDescription" rows="3" class="field-input" placeholder="Describe your store..."></textarea>

                <label class="field-label mt-5">Store Email</label>
                <input id="contactEmail" name="contactEmail" type="email" class="field-input" placeholder="store@example.com" value="<?php echo htmlspecialchars($defaultEmail ?? ''); ?>" required>

                <label class="field-label mt-5">WhatsApp Number</label>
                <input id="contactPhone" name="contactPhone" class="field-input" placeholder="+234..." value="<?php echo htmlspecialchars($defaultPhone ?? ''); ?>" required>

                <div class="mt-8 flex items-center justify-between gap-4">
                    <a href="/dashboard" class="btn-secondary px-8 py-3 rounded-2xl text-center">Cancel</a>
                    <button type="submit" id="submit-btn" class="btn-primary px-8 py-3 rounded-2xl">Create Store</button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
.field-label { display:block; margin-bottom:8px; margin-left:4px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#6b7280; }
.field-input { width:100%; border-radius:16px; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.04); color:#fff; padding:14px 16px; outline:none; }
.field-input:focus { border-color: rgba(255,97,10,.5); background: rgba(255,255,255,.08); }
.btn-primary { background:#ff610a; color:#fff; font-weight:900; box-shadow:0 12px 30px rgba(255,97,10,.25); }
.btn-primary:hover { background:#e05500; }
.btn-secondary { background:rgba(255,255,255,.08); color:#fff; border:1px solid rgba(255,255,255,.12); font-weight:800; }
.btn-secondary:hover { background: rgba(255,255,255,.12); }
</style>

<script>
(() => {
  const slugify = (v) => v.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
  const storeName = document.getElementById('storeName');
  const preview = document.getElementById('preview-url');
  storeName.addEventListener('input', () => {
    const slug = slugify(storeName.value || 'your-store');
    preview.textContent = `/store/${slug}`;
  });

  const errorBox = document.getElementById('error-box');
  const setError = (msg) => {
    errorBox.textContent = msg || '';
    errorBox.classList.toggle('hidden', !msg);
  };

  document.getElementById('create-store-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
      storeName: document.getElementById('storeName').value.trim(),
      storeDescription: document.getElementById('storeDescription').value.trim(),
      contactPhone: document.getElementById('contactPhone').value.trim(),
      contactEmail: document.getElementById('contactEmail').value.trim(),
    };

    if (!payload.storeName) return setError('Please enter your store name');
    if (!payload.contactEmail) return setError('Please enter your store email');
    if (!payload.contactPhone) return setError('Please enter your WhatsApp number');

    setError('');
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = 'Creating...';

    try {
      const res = await fetch('/api/store_create.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (!res.ok || !data.success) {
        setError(data.error || 'Failed to create store');
      } else {
        window.location.href = '/dashboard/' + data.storeSlug;
      }
    } catch (e) {
      console.error('Store creation error:', e);
      setError('Something went wrong. Error: ' + (e.message || 'unknown'));
    } finally {
      btn.disabled = false;
      btn.textContent = 'Create Store';
    }
  });
})();
</script>
<?php
$content = ob_get_clean();
?>