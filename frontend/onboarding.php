<?php
/*
 * Onboarding / registration page template (no store required).
 */

$pageTitle = 'Create Account - vomp';
ob_start();
?>
<section class="min-h-[78vh] flex items-center justify-center py-10">
    <div class="w-full max-w-xl glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 bg-[#ff610a]/10 blur-[80px] rounded-full"></div>
        <div class="relative z-10">
            <div id="error-box" class="hidden px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold mb-6"></div>

            <div id="register-panel">
                <h2 class="text-4xl font-black text-white mb-2 tracking-tight">Join the future.</h2>
                <p class="text-gray-400 font-medium mb-8">Create your account in seconds. Add a store later.</p>

                <form id="register-form" class="space-y-6">
                    <label class="field-label">Full Name</label>
                    <input id="name" name="name" class="field-input" placeholder="Amara Okafor" required>

                    <label class="field-label mt-5">Email Address</label>
                    <input id="email" type="email" name="email" class="field-input" placeholder="you@example.com" required>

                    <label class="field-label mt-5">Phone Number</label>
                    <input id="phone" type="tel" name="phone" class="field-input" placeholder="+234..." required>

                    <label class="field-label mt-5">Password</label>
                    <input id="password" type="password" name="password" class="field-input" placeholder="At least 6 characters" required>

                    <div class="mt-8 flex items-center justify-between gap-4">
                        <a href="/login" class="text-sm font-medium text-gray-500 hover:text-[#ff8c3a]">Already have an account?</a>
                        <button type="submit" id="register-btn" class="btn-primary px-8 py-3 rounded-2xl">Create Account</button>
                    </div>
                </form>
            </div>

            <div id="success-panel" class="hidden text-center">
                <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <h2 class="text-3xl font-black text-white mb-2">Account Created</h2>
                <p class="text-gray-400 font-medium mb-8">Welcome to vomp. Your account is ready.</p>
                <div class="flex flex-col gap-3">
                    <a href="/dashboard" class="btn-primary px-8 py-4 rounded-2xl text-center">Go to Dashboard</a>
                    <a href="/dashboard/create-store" class="btn-secondary px-8 py-4 rounded-2xl text-center">Create a Store</a>
                </div>
            </div>
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
  const errorBox = document.getElementById('error-box');
  const setError = (msg) => {
    errorBox.textContent = msg || '';
    errorBox.classList.toggle('hidden', !msg);
  };

  document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;

    if (!name) return setError('Please enter your full name');
    if (!email.includes('@')) return setError('Please enter a valid email');
    if (!phone) return setError('Please enter your phone number');
    if (password.length < 6) return setError('Password must be at least 6 characters');

    setError('');
    const btn = document.getElementById('register-btn');
    btn.disabled = true;
    btn.textContent = 'Creating...';

    try {
      const res = await fetch('/api/register.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ name, email, phone, password })
      });

      const data = await res.json();
      if (!res.ok) {
        setError(data.error || 'Registration failed');
      } else {
        document.getElementById('register-panel').classList.add('hidden');
        document.getElementById('success-panel').classList.remove('hidden');
      }
    } catch (e) {
      console.error('Registration error:', e);
      if (e instanceof SyntaxError && e.message.includes('JSON')) {
        setError('Server returned non-JSON response. Check console for details.');
      } else {
        setError('Network error. Please try again.');
      }
    } finally {
      btn.disabled = false;
      btn.textContent = 'Create Account';
    }
  });
})();
</script>
<?php
$content = ob_get_clean();
?>