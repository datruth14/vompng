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
<?php
$content = ob_get_clean();
?>
