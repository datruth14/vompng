<?php
$pageTitle = 'Admin Users - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header>
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Super Admin / Users</p>
        <h1 class="text-5xl font-black text-white tracking-tight">All Users</h1>
    </header>

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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                        <td class="p-3 text-white font-bold"><?php echo htmlspecialchars($u['name']); ?></td>
                        <td class="p-3 text-gray-400"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td class="p-3 text-gray-400"><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td>
                        <td class="p-3 text-white"><?php echo (int) ($u['store_count'] ?? 0); ?></td>
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
            <?php if ($page > 1): ?>
                <a href="/admin/users?page=<?php echo $page - 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
                <a href="/admin/users?page=<?php echo $i; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="/admin/users?page=<?php echo $page + 1; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
?>
