<?php
$pageTitle = 'Profile - vomp';
ob_start();
?>
<section class="py-8 md:py-12">
    <div class="mb-10">
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Account</p>
        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2">My Profile</h1>
        <p class="text-gray-400">Update your name, email, and password.</p>
    </div>

    <div class="max-w-2xl">
        <form id="profileForm" class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-white/10 space-y-5">
            <div>
                <label class="text-sm text-gray-300 font-bold">Full Name</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($currentUser['name']); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors" />
            </div>

            <div>
                <label class="text-sm text-gray-300 font-bold">Email Address</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors" />
            </div>

            <div>
                <label class="text-sm text-gray-300 font-bold">Phone Number</label>
                <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors" />
            </div>

            <hr class="border-white/10">

            <div>
                <label class="text-sm text-gray-300 font-bold">Current Password</label>
                <input type="password" name="current_password" id="current_password" placeholder="Required to change password" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors" />
            </div>

            <div>
                <label class="text-sm text-gray-300 font-bold">New Password</label>
                <input type="password" name="password" id="password" placeholder="Leave blank to keep current" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors" />
            </div>

            <div>
                <label class="text-sm text-gray-300 font-bold">Confirm New Password</label>
                <input type="password" name="password_confirm" id="password_confirm" placeholder="Re-enter new password" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors" />
            </div>

    <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" id="saveBtn" class="btn-press px-6 py-3 rounded-2xl bg-[#ff610a] text-white font-black">Save Changes</button>
    </div>

    <div id="msg" class="mt-3"></div>
</form>

<!-- Transaction PIN Section -->
<div class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-white/10 mt-8">
    <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Security</p>
    <h2 class="text-2xl font-black text-white mb-2">Transaction PIN</h2>
    <p class="text-gray-400 text-sm mb-6">Used to authorize token purchases, transfers, withdrawals, and bill payments.</p>

    <div class="space-y-5 max-w-md">
        <div>
            <label class="text-sm text-gray-300 font-bold"><?php echo empty($currentUser['transaction_pin']) ? 'New PIN' : 'Current PIN'; ?></label>
            <input type="password" id="pinCurrent" maxlength="4" inputmode="numeric" pattern="[0-9]*" autocomplete="off" placeholder="••••" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors text-center text-2xl tracking-[0.5em]" />
        </div>
        <?php if (!empty($currentUser['transaction_pin'])): ?>
        <div>
            <label class="text-sm text-gray-300 font-bold">New PIN</label>
            <input type="password" id="pinNew" maxlength="4" inputmode="numeric" pattern="[0-9]*" autocomplete="off" placeholder="••••" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors text-center text-2xl tracking-[0.5em]" />
        </div>
        <div>
            <label class="text-sm text-gray-300 font-bold">Confirm New PIN</label>
            <input type="password" id="pinConfirm" maxlength="4" inputmode="numeric" pattern="[0-9]*" autocomplete="off" placeholder="••••" class="mt-2 w-full rounded-xl px-4 py-3 bg-transparent border border-white/5 focus:border-[#ff610a] focus:outline-none transition-colors text-center text-2xl tracking-[0.5em]" />
        </div>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-3 pt-4">
        <button type="button" id="pinSaveBtn" class="btn-press px-6 py-3 rounded-2xl bg-[#ff610a] text-white font-black"><?php echo empty($currentUser['transaction_pin']) ? 'Set PIN' : 'Change PIN'; ?></button>
    </div>
    <div id="pinMsg" class="mt-3"></div>
</div>
    </div>
</section>

<script>
// Transaction PIN
document.getElementById('pinSaveBtn')?.addEventListener('click', async function() {
    var btn = this;
    var cur = document.getElementById('pinCurrent').value.trim();
    var newPin = document.getElementById('pinNew') ? document.getElementById('pinNew').value.trim() : '';
    var confirmPin = document.getElementById('pinConfirm') ? document.getElementById('pinConfirm').value.trim() : '';
    var hasExisting = <?php echo empty($currentUser['transaction_pin']) ? 'false' : 'true'; ?>;

    if (hasExisting) {
        if (!newPin || newPin.length !== 4 || !/^\d{4}$/.test(newPin)) {
            document.getElementById('pinMsg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">Enter a valid 4-digit new PIN</div>';
            return;
        }
        if (newPin !== confirmPin) {
            document.getElementById('pinMsg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">PINs do not match</div>';
            return;
        }
    } else {
        if (!cur || cur.length !== 4 || !/^\d{4}$/.test(cur)) {
            document.getElementById('pinMsg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">Enter a valid 4-digit PIN</div>';
            return;
        }
    }

    btn.disabled = true;
    btn.textContent = 'Saving...';

    var body = hasExisting ? { pin: newPin, current_pin: cur } : { pin: cur };

    try {
        var res = await fetch('/api/set_pin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        var json = await res.json();
        if (json.success) {
            document.getElementById('pinMsg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 font-bold">PIN ' + (hasExisting ? 'changed' : 'set') + ' successfully</div>';
            document.getElementById('pinCurrent').value = '';
            if (document.getElementById('pinNew')) document.getElementById('pinNew').value = '';
            if (document.getElementById('pinConfirm')) document.getElementById('pinConfirm').value = '';
            if (!hasExisting) {
                setTimeout(function() { location.reload(); }, 1500);
            }
        } else {
            document.getElementById('pinMsg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">' + (json.error || 'Failed to save PIN') + '</div>';
        }
    } catch (err) {
        document.getElementById('pinMsg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">Network error. Please try again.</div>';
    }

    btn.disabled = false;
    btn.textContent = hasExisting ? 'Change PIN' : 'Set PIN';
});

document.getElementById('saveBtn').addEventListener('click', async function() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirm').value;
    const currentPassword = document.getElementById('current_password').value;

    if (password && password.length < 6) {
        document.getElementById('msg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">Password must be at least 6 characters</div>';
        btn.disabled = false;
        btn.textContent = 'Save Changes';
        return;
    }

    if (password && password !== confirm) {
        document.getElementById('msg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">Passwords do not match</div>';
        btn.disabled = false;
        btn.textContent = 'Save Changes';
        return;
    }

    const body = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
    };
    if (password) {
        if (!currentPassword) {
            document.getElementById('msg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">Enter your current password to change it</div>';
            btn.disabled = false;
            btn.textContent = 'Save Changes';
            return;
        }
        body.password = password;
        body.current_password = currentPassword;
    }

    try {
        const res = await fetch('/api/profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const json = await res.json();
        const msg = document.getElementById('msg');
        if (json.success) {
            msg.innerHTML = '<div class="px-4 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 font-bold">Profile updated successfully</div>';
            document.getElementById('password').value = '';
            document.getElementById('password_confirm').value = '';
        } else {
            msg.innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">' + (json.error || 'Failed to update profile') + '</div>';
        }
    } catch (err) {
        document.getElementById('msg').innerHTML = '<div class="px-4 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 font-bold">Network error. Please try again.</div>';
    }

    btn.disabled = false;
    btn.textContent = 'Save Changes';
});
</script>

<?php
$content = ob_get_clean();
?>
