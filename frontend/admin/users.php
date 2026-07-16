<?php
$pageTitle = 'Admin Users - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header>
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Super Admin / Users</p>
        <h1 class="text-5xl font-black text-white tracking-tight">All Users</h1>
    </header>

    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" class="flex gap-3 flex-1 max-w-md">
            <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Search by name, email, or phone..." class="flex-1 bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm">
            <button type="submit" class="px-5 py-3 rounded-2xl bg-[#ff610a] text-white font-bold text-sm hover:bg-[#e05500] transition-all">Search</button>
            <?php if (!empty($_GET['q'])): ?>
                <a href="/admin/users" class="px-5 py-3 rounded-2xl bg-white/5 border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/10 transition-all">Clear</a>
            <?php endif; ?>
        </form>
        <a href="/api/admin/export?type=users" class="px-5 py-3 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 font-bold text-sm hover:bg-emerald-600/30 transition-all whitespace-nowrap">Export CSV</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-500 uppercase tracking-wider text-xs font-black">
                    <th class="text-left p-3">Name</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Phone</th>
                    <th class="text-left p-3">Stores</th>
                    <th class="text-left p-3">Tokens</th>
                    <th class="text-left p-3">Plan</th>
                    <th class="text-left p-3">Role</th>
                    <th class="text-left p-3">Joined</th>
                    <th class="text-left p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                        <td class="p-3 text-white font-bold"><?php echo htmlspecialchars($u['name']); ?></td>
                        <td class="p-3 text-gray-400"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td class="p-3">
                            <?php if (!empty($u['phone'])): ?>
                                <?php
                                $wa = preg_replace('/\D+/', '', $u['phone']);
                                if (!str_starts_with($wa, '234')) $wa = '234' . $wa;
                                ?>
                                <a href="https://wa.me/<?php echo $wa; ?>" target="_blank" class="text-gray-400 hover:text-emerald-400 transition-colors flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    <?php echo htmlspecialchars($u['phone']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-gray-500">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-white"><?php echo number_format((int) ($u['store_count'] ?? 0)); ?></td>
                        <td class="p-3 text-white"><?php echo number_format((int) ($u['token_balance'] ?? 0)); ?></td>
                        <td class="p-3">
                            <?php if (($u['plan'] ?? 'free') === 'premium'): ?>
                                <span class="text-emerald-400 font-bold">PREMIUM</span>
                            <?php else: ?>
                                <span class="text-gray-500">free</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3">
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="text-[#ff610a] font-bold">ADMIN</span>
                            <?php else: ?>
                                <span class="text-gray-500">user</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-gray-500 text-xs whitespace-nowrap"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                        <td class="p-3">
                            <button onclick="openResetPassword('<?php echo $u['id']; ?>', '<?php echo htmlspecialchars(addslashes($u['name'])); ?>')" class="px-3 py-1.5 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-bold hover:bg-amber-500/30 transition-all">Reset Password</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <tr><td colspan="8" class="p-6 text-center text-gray-500">No users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-2 mt-8">
            <?php $qs = !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?>
            <?php if ($page > 1): ?>
                <a href="/admin/users?page=<?php echo $page - 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="/admin/users?page=<?php echo $i . $qs; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="/admin/users?page=<?php echo $page + 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm" style="display:none">
    <div class="bg-gray-900 border border-white/10 rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-black text-white">Reset Password</h3>
            <button onclick="closeResetPassword()" class="p-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <p class="text-gray-400 text-sm mb-4">Resetting password for: <strong id="resetUserName" class="text-white"></strong></p>
        <form id="resetPasswordForm" onsubmit="return false;">
            <input type="hidden" name="user_id" id="resetUserId">
            <div class="mb-4">
                <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">New Password</label>
                <input type="password" name="password" id="resetPassword" required minlength="6" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#ff610a]/50 transition-all" placeholder="Min 6 characters">
            </div>
            <div class="mb-4">
                <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Confirm New Password</label>
                <input type="password" id="resetPasswordConfirm" required minlength="6" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#ff610a]/50 transition-all" placeholder="Repeat password">
            </div>
            <div id="resetPasswordError" class="text-red-400 text-sm mb-4 hidden"></div>
            <div class="flex gap-3">
                <button type="button" onclick="closeResetPassword()" class="flex-1 px-4 py-3 rounded-xl bg-white/5 border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/10 transition-all">Cancel</button>
                <button type="submit" onclick="submitResetPassword()" class="flex-1 px-4 py-3 rounded-xl bg-[#ff610a] text-white font-bold text-sm hover:bg-[#e05500] transition-all">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<script>
let resetUserId = '';
function openResetPassword(userId, userName) {
    resetUserId = userId;
    document.getElementById('resetUserId').value = userId;
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('resetPassword').value = '';
    document.getElementById('resetPasswordConfirm').value = '';
    document.getElementById('resetPasswordError').classList.add('hidden');
    document.getElementById('resetPasswordModal').style.display = 'flex';
}
function closeResetPassword() {
    document.getElementById('resetPasswordModal').style.display = 'none';
}
function submitResetPassword() {
    const password = document.getElementById('resetPassword').value;
    const confirm = document.getElementById('resetPasswordConfirm').value;
    const errorEl = document.getElementById('resetPasswordError');

    if (!password || password.length < 6) {
        errorEl.textContent = 'Password must be at least 6 characters';
        errorEl.classList.remove('hidden');
        return;
    }
    if (password !== confirm) {
        errorEl.textContent = 'Passwords do not match';
        errorEl.classList.remove('hidden');
        return;
    }

    errorEl.classList.add('hidden');

    const btn = document.querySelector('#resetPasswordForm button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Resetting...';

    fetch('/api/admin/reset_password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: resetUserId, password })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeResetPassword();
            showToast('Password reset successful');
        } else {
            errorEl.textContent = data.error || 'Failed to reset password';
            errorEl.classList.remove('hidden');
        }
    })
    .catch(() => {
        errorEl.textContent = 'Network error. Please try again.';
        errorEl.classList.remove('hidden');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Reset Password';
    });
}

function showToast(msg) {
    const el = document.createElement('div');
    el.className = 'fixed bottom-6 right-6 z-50 px-6 py-3 rounded-2xl bg-emerald-600 text-white font-bold text-sm shadow-xl animate__animated animate__fadeInUp';
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => { el.classList.add('animate__fadeOutDown'); setTimeout(() => el.remove(), 500); }, 3000);
}
</script>

<?php
$content = ob_get_clean();
?>
