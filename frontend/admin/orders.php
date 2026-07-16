<?php
$pageTitle = 'Admin Transactions & Withdrawals - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-8">
    <header>
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Super Admin / Finance</p>
        <h1 class="text-5xl font-black text-white tracking-tight">Transactions & Withdrawals</h1>
    </header>

    <!-- Commission Summary -->
    <?php $cs = $commissionSummary; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-morphism rounded-2xl p-6 border border-white/10">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-1">Total Withdrawals</p>
            <p class="text-3xl font-black text-white"><?php echo number_format($cs['total_withdrawals']); ?></p>
        </div>
        <div class="glass-morphism rounded-2xl p-6 border border-white/10">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-1">Total NGN Withdrawn</p>
            <p class="text-3xl font-black text-[#ff610a]">₦<?php echo number_format($cs['total_naira_withdrawn']); ?></p>
        </div>
        <div class="glass-morphism rounded-2xl p-6 border border-emerald-500/20" style="background: rgba(5,150,105,0.08);">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-1">Withdraw Commission (2%)</p>
            <p class="text-3xl font-black text-emerald-400">₦<?php echo number_format($cs['total_commission']); ?></p>
        </div>
        <div class="glass-morphism rounded-2xl p-6 border border-sky-500/20" style="background: rgba(14,165,233,0.08);">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-1">Bill Payment Commission (<?php echo BILL_COMMISSION_PERCENT; ?>%)</p>
            <p class="text-3xl font-black text-sky-400">₦<?php echo number_format($cs['total_bill_commission'], 2); ?></p>
            <p class="text-xs text-gray-500 mt-1"><?php echo number_format($cs['total_bill_payments']); ?> payments</p>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <section class="glass-morphism rounded-3xl p-6 md:p-8 border border-white/10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-white">Withdrawal Requests</h2>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex gap-3">
                    <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Search user/bank..." class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm w-40 md:w-auto">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#ff610a] text-white font-bold text-sm hover:bg-[#e05500] transition-all">Search</button>
                    <?php if (!empty($_GET['q'])): ?>
                        <a href="/admin/orders" class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/10 transition-all">Clear</a>
                    <?php endif; ?>
                </form>
                <a href="/api/admin/export?type=withdrawals" class="px-4 py-2.5 rounded-xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 font-bold text-sm hover:bg-emerald-600/30 transition-all whitespace-nowrap">Export CSV</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-500 uppercase tracking-wider text-xs font-black">
                        <th class="text-left p-3">User</th>
                        <th class="text-left p-3">Amount</th>
                        <th class="text-left p-3">Commission</th>
                        <th class="text-left p-3">Bank</th>
                        <th class="text-left p-3">Account</th>
                        <th class="text-left p-3">Status</th>
                        <th class="text-left p-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($withdrawals as $w): ?>
                        <?php $commission = (int) ($w['naira_amount'] * 0.02); ?>
                        <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                            <td class="p-3">
                                <p class="text-white font-bold"><?php echo htmlspecialchars($w['user_name'] ?? 'Unknown'); ?></p>
                                <p class="text-gray-500 text-xs"><?php echo htmlspecialchars($w['user_email'] ?? ''); ?></p>
                            </td>
                            <td class="p-3 text-white font-bold"><?php echo number_format((int) $w['amount']); ?> VC<br><span class="text-xs text-gray-400">₦<?php echo number_format((int) $w['naira_amount']); ?></span></td>
                            <td class="p-3 text-emerald-400 font-bold">₦<?php echo number_format($commission); ?></td>
                            <td class="p-3 text-gray-400"><?php echo htmlspecialchars($w['bank_name'] ?? ''); ?></td>
                            <td class="p-3 text-gray-300 font-mono"><?php echo htmlspecialchars($w['account_number'] ?? ''); ?><br><span class="text-xs text-gray-500"><?php echo htmlspecialchars($w['account_name'] ?? ''); ?></span></td>
                            <td class="p-3">
                                <?php if ($w['status'] === 'success'): ?>
                                    <span class="text-emerald-400 font-bold">Success</span>
                                <?php elseif ($w['status'] === 'pending'): ?>
                                    <span class="text-yellow-400 font-bold">Pending</span>
                                <?php elseif ($w['status'] === 'otp'): ?>
                                    <span class="text-yellow-400 font-bold">OTP</span>
                                <?php else: ?>
                                    <span class="text-rose-400 font-bold"><?php echo htmlspecialchars($w['status'] ?? 'Failed'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-gray-500 text-xs whitespace-nowrap"><?php echo date('M j, Y g:i A', strtotime($w['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$withdrawals): ?>
                        <tr><td colspan="7" class="p-6 text-center text-gray-500">No withdrawals yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($withdrawTotalPages > 1): ?>
            <div class="flex items-center justify-center gap-2 mt-8">
                <?php $qs = !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?>
                <?php if ($page > 1): ?>
                    <a href="/admin/orders?page=<?php echo $page - 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
                <?php endif; ?>
                <?php
                $start = max(1, $page - 2);
                $end = min($withdrawTotalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="/admin/orders?page=<?php echo $i . $qs; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $withdrawTotalPages): ?>
                    <a href="/admin/orders?page=<?php echo $page + 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Token Transactions Table -->
    <section class="glass-morphism rounded-3xl p-6 md:p-8 border border-white/10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-white">Token Transactions</h2>
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="flex gap-3">
                    <input type="text" name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" placeholder="Search..." class="bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all text-sm w-40 md:w-auto">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#ff610a] text-white font-bold text-sm hover:bg-[#e05500] transition-all">Search</button>
                    <?php if (!empty($_GET['q'])): ?>
                        <a href="/admin/orders" class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/10 transition-all">Clear</a>
                    <?php endif; ?>
                </form>
                <a href="/api/admin/export?type=transactions" class="px-4 py-2.5 rounded-xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 font-bold text-sm hover:bg-emerald-600/30 transition-all whitespace-nowrap">Export CSV</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-500 uppercase tracking-wider text-xs font-black">
                        <th class="text-left p-3">Store / User</th>
                        <th class="text-left p-3">Type</th>
                        <th class="text-left p-3">Amount</th>
                        <th class="text-left p-3">Description</th>
                        <th class="text-left p-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $t): ?>
                        <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                            <td class="p-3">
                                <?php if (!empty($t['store_slug'])): ?>
                                    <a href="/store/<?php echo htmlspecialchars($t['store_slug'] ?? ''); ?>" target="_blank" class="text-white font-bold hover:text-[#ff610a] transition-colors">
                                        <?php echo htmlspecialchars($t['store_name'] ?? '-'); ?>
                                    </a>
                                <?php elseif (!empty($t['user_id'])): ?>
                                    <span class="text-gray-400 text-sm">User: <?php echo htmlspecialchars($t['user_id']); ?></span>
                                <?php else: ?>
                                    <span class="text-gray-500">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <?php if ($t['type'] === 'credit'): ?>
                                    <span class="text-emerald-400 font-bold">Credit</span>
                                <?php else: ?>
                                    <span class="text-rose-400 font-bold">Debit</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-white font-bold"><?php echo ($t['type'] === 'credit' ? '+' : '-') . number_format((int) $t['amount']); ?></td>
                            <td class="p-3 text-gray-400 max-w-xs truncate"><?php echo htmlspecialchars($t['description'] ?? ''); ?></td>
                            <td class="p-3 text-gray-500 text-xs whitespace-nowrap"><?php echo date('M j, Y g:i A', strtotime($t['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$transactions): ?>
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">No transactions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
            <div class="flex items-center justify-center gap-2 mt-8">
                <?php $qs = !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : ''; ?>
                <?php if ($page > 1): ?>
                    <a href="/admin/orders?page=<?php echo $page - 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">← Prev</a>
                <?php endif; ?>
                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="/admin/orders?page=<?php echo $i . $qs; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i === $page ? 'bg-[#ff610a] text-white' : 'bg-white/5 border border-white/10 text-gray-400 hover:bg-white/10'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="/admin/orders?page=<?php echo $page + 1 . $qs; ?>" class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-white font-bold text-sm hover:bg-white/10 transition-all">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Bill Payments Table -->
    <section class="glass-morphism rounded-3xl p-6 md:p-8 border border-white/10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-white">Bill Payments</h2>
            <div class="flex items-center gap-3">
                <p class="text-sm text-gray-500"><?php echo number_format($billPaymentsTotal); ?> total</p>
                <a href="/api/admin/export?type=bill_payments" class="px-4 py-2.5 rounded-xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-300 font-bold text-sm hover:bg-emerald-600/30 transition-all whitespace-nowrap">Export CSV</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-500 uppercase tracking-wider text-xs font-black">
                        <th class="text-left p-3">User</th>
                        <th class="text-left p-3">Type</th>
                        <th class="text-left p-3">Service</th>
                        <th class="text-left p-3">Customer</th>
                        <th class="text-left p-3">Amount</th>
                        <th class="text-left p-3">Commission</th>
                        <th class="text-left p-3">Status</th>
                        <th class="text-left p-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($billPayments as $bp): ?>
                        <tr class="border-t border-white/5 hover:bg-white/[0.02]">
                            <td class="p-3">
                                <p class="text-white font-bold"><?php echo htmlspecialchars($bp['user_name'] ?? 'Unknown'); ?></p>
                                <p class="text-gray-500 text-xs"><?php echo htmlspecialchars($bp['user_email'] ?? ''); ?></p>
                            </td>
                            <td class="p-3">
                                <span class="capitalize text-white font-bold"><?php echo htmlspecialchars($bp['type']); ?></span>
                            </td>
                            <td class="p-3 text-gray-400"><?php echo htmlspecialchars($bp['service_id']); ?></td>
                            <td class="p-3 text-gray-400 font-mono"><?php echo htmlspecialchars($bp['customer_id'] ?? '-'); ?></td>
                            <td class="p-3 text-white font-bold">₦<?php echo number_format((float) $bp['amount_naira'], 2); ?><br><span class="text-xs text-gray-500"><?php echo number_format((int) $bp['coins_deducted']); ?> VC</span></td>
                            <td class="p-3 text-sky-400 font-bold">₦<?php echo number_format((float) ($bp['commission'] ?? 0), 2); ?></td>
                            <td class="p-3">
                                <?php if ($bp['status'] === 'completed'): ?>
                                    <span class="text-emerald-400 font-bold">Completed</span>
                                <?php elseif ($bp['status'] === 'processing'): ?>
                                    <span class="text-yellow-400 font-bold">Processing</span>
                                <?php else: ?>
                                    <span class="text-rose-400 font-bold"><?php echo htmlspecialchars($bp['status'] ?? 'Failed'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-gray-500 text-xs whitespace-nowrap"><?php echo date('M j, Y g:i A', strtotime($bp['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$billPayments): ?>
                        <tr><td colspan="8" class="p-6 text-center text-gray-500">No bill payments yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
<?php
$content = ob_get_clean();
?>