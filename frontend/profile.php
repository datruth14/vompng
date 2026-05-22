<?php
$pageTitle = 'Profile - VomP';
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

            <hr class="border-white/10">

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
    </div>
</section>

<script>
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
