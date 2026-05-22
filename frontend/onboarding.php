<?php
/*
 * Onboarding / registration page template.
 */

$pageTitle = 'Onboarding - VomP';
ob_start();
?>
<section class="min-h-[78vh] flex items-center justify-center py-10">
    <div class="w-full max-w-3xl glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 bg-[#ff610a]/10 blur-[80px] rounded-full"></div>
        <div class="relative z-10">
            <div id="step-indicator" class="flex items-center justify-center gap-3 mb-10">
                <div class="step-pill active" data-step="1">1 Account</div>
                <div class="step-line"></div>
                <div class="step-pill" data-step="2">2 Store</div>
                <div class="step-line"></div>
                <div class="step-pill" data-step="3">3 Live</div>
            </div>

            <div id="error-box" class="hidden px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold mb-6"></div>

            <form id="onboarding-form" class="space-y-6">
                <div class="step-panel" data-step="1">
                    <h2 class="text-4xl font-black text-white mb-2 tracking-tight">Join the future.</h2>
                    <p class="text-gray-400 font-medium mb-8">Create your seller account in seconds.</p>

                    <label class="field-label">Full Name</label>
                    <input id="name" name="name" class="field-input" placeholder="Amara Okafor" required>

                    <label class="field-label mt-5">Email Address</label>
                    <input id="email" type="email" name="email" class="field-input" placeholder="you@example.com" required>

                    <label class="field-label mt-5">Password</label>
                    <input id="password" type="password" name="password" class="field-input" placeholder="••••••••" required>

                    <div class="mt-8 flex items-center justify-between gap-4">
                        <a href="/login" class="text-sm font-medium text-gray-500 hover:text-[#ff8c3a]">Already a seller? Sign in</a>
                        <button type="button" id="next-1" class="btn-primary px-8 py-3 rounded-2xl">Continue</button>
                    </div>
                </div>

                <div class="step-panel hidden" data-step="2">
                    <h2 class="text-4xl font-black text-white mb-2 tracking-tight">Tell us about your shop.</h2>
                    <p class="text-gray-400 font-medium mb-8">Your brand, your rules.</p>

                    <label class="field-label">Store Name</label>
                    <input id="storeName" name="storeName" class="field-input" placeholder="e.g. Amara's Gems" required>

                    <div class="mt-3 p-3 rounded-xl bg-[#ff610a]/5 border border-[#ff610a]/10">
                        <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Preview URL</p>
                        <p id="preview-url" class="text-[#ff610a] font-mono text-xs truncate">/store/your-store</p>
                    </div>

                    <label class="field-label mt-5">Store Bio</label>
                    <textarea id="storeDescription" name="storeDescription" rows="3" class="field-input" placeholder="Describe your magic..."></textarea>

                    <label class="field-label mt-5">WhatsApp Number</label>
                    <input id="contactPhone" name="contactPhone" class="field-input" placeholder="+234..." required>

                    <div class="mt-8 flex items-center justify-between gap-4">
                        <button type="button" id="back-2" class="btn-secondary px-8 py-3 rounded-2xl">Back</button>
                        <button type="button" id="submit-onboarding" class="btn-primary px-8 py-3 rounded-2xl">Create Store</button>
                    </div>
                </div>

                <div class="step-panel hidden" data-step="3">
                    <h2 class="text-4xl font-black text-white mb-2 tracking-tight">You are live.</h2>
                    <p class="text-gray-400 font-medium mb-8">Your storefront has been created successfully.</p>

                    <div class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 mb-6">
                        <p class="text-[10px] uppercase tracking-widest text-emerald-300 font-bold mb-2">Store URL</p>
                        <a id="live-store-link" href="#" target="_blank" class="text-emerald-200 font-mono text-sm break-all"></a>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <a id="go-dashboard" href="/dashboard" class="btn-primary px-8 py-3 rounded-2xl">Go To Dashboard</a>
                        <a id="view-store" href="#" target="_blank" class="btn-secondary px-8 py-3 rounded-2xl">View Store</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
.step-pill { padding: 8px 14px; border-radius: 9999px; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; border: 1px solid rgba(255,255,255,.08); color: #6b7280; background: rgba(255,255,255,.03); }
.step-pill.active { color: #fff; background: #ff610a; border-color: rgba(255,140,60,.7); box-shadow: 0 8px 30px rgba(255,97,10,.28); }
.step-line { width: 20px; height: 1px; background: rgba(255,255,255,.12); }
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
  let step = 1;
  const panels = [...document.querySelectorAll('.step-panel')];
  const pills = [...document.querySelectorAll('.step-pill')];
  const errorBox = document.getElementById('error-box');

  const setStep = (n) => {
    step = n;
    panels.forEach((p) => p.classList.toggle('hidden', Number(p.dataset.step) !== n));
    pills.forEach((p) => p.classList.toggle('active', Number(p.dataset.step) <= n));
  };

  const slugify = (v) => v.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
  const storeName = document.getElementById('storeName');
  const preview = document.getElementById('preview-url');
  storeName.addEventListener('input', () => {
    const slug = slugify(storeName.value || 'your-store');
    preview.textContent = `/store/${slug}`;
  });

  const setError = (msg) => {
    errorBox.textContent = msg || '';
    errorBox.classList.toggle('hidden', !msg);
  };

  document.getElementById('next-1').addEventListener('click', () => {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    if (!name) return setError('Please enter your full name');
    if (!email.includes('@')) return setError('Please enter a valid email');
    if (password.length < 6) return setError('Password must be at least 6 characters');
    setError('');
    setStep(2);
  });

  document.getElementById('back-2').addEventListener('click', () => setStep(1));

  document.getElementById('submit-onboarding').addEventListener('click', async () => {
    const payload = {
      name: document.getElementById('name').value.trim(),
      email: document.getElementById('email').value.trim(),
      password: document.getElementById('password').value,
      storeName: document.getElementById('storeName').value.trim(),
      storeDescription: document.getElementById('storeDescription').value.trim(),
      contactPhone: document.getElementById('contactPhone').value.trim(),
    };

    if (!payload.storeName) return setError('Please enter your store name');
    if (!payload.contactPhone) return setError('Please enter your WhatsApp number');

    setError('');
    const btn = document.getElementById('submit-onboarding');
    btn.disabled = true;
    btn.textContent = 'Creating...';

    try {
      const res = await fetch('/api/register.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (!res.ok) {
        setError(data.error || 'Registration failed');
      } else {
        const storeUrl = `${window.location.origin}/store/${data.storeSlug}`;
        const dashboardUrl = `${window.location.origin}/dashboard/${data.storeSlug}`;
        const storeLink = document.getElementById('live-store-link');
        storeLink.href = storeUrl;
        storeLink.textContent = storeUrl;
        document.getElementById('view-store').href = storeUrl;
        document.getElementById('go-dashboard').href = dashboardUrl;
        setStep(3);
      }
    } catch (e) {
      setError('Something went wrong. Please try again.');
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