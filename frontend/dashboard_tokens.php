<?php
/*
 * Dashboard token management template.
 */

$pageTitle = 'Token Management - VomP';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Order Credits</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">Tokens</h1>
            <p class="text-gray-500 font-medium text-lg">Manage your storefront's order processing capacity.</p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article class="glass-morphism rounded-[2.5rem] p-10 border border-white/10 md:col-span-1 flex flex-col items-center justify-center text-center">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-4">Current Balance</p>
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-[#ff610a]/20 blur-3xl rounded-full"></div>
                <p class="text-7xl font-black text-white relative"><?php echo (int) $store['token_balance']; ?></p>
            </div>
            <p class="text-gray-400 text-sm font-medium">Tokens available for <br>customer orders</p>
        </article>

        <div class="md:col-span-2 space-y-8">
            <h2 class="text-2xl font-black text-white">Top Up Tokens</h2>
            <div class="glass-morphism rounded-3xl p-8 border border-white/10">
                <p class="text-xs uppercase tracking-[0.2em] font-black text-gray-500 mb-1">Price</p>
                <p class="text-3xl font-black text-white mb-6">₦20 <span class="text-sm text-gray-500 font-medium">per token</span></p>

                <div class="flex flex-col sm:flex-row gap-6 items-end">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Number of Tokens</label>
                        <input type="number" id="tokenInput" min="50" step="1" value="50" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all text-lg font-black">
                        <p class="text-xs text-gray-500 mt-2 ml-1">Minimum: <span class="text-white font-bold">50 tokens</span> (₦1,000)</p>
                    </div>
                    <div class="w-full sm:w-48 text-center sm:text-right">
                        <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-1">Total Price</p>
                        <p id="totalPrice" class="text-3xl font-black text-[#ff610a]">₦1,000</p>
                    </div>
                </div>

                <button id="purchaseBtn" class="btn-press w-full py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all mt-8">
                    Buy Tokens
                </button>
                <div id="purchaseMsg" class="mt-4"></div>
            </div>
        </div>
    </div>

    <section class="glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-white">Transaction History</h2>
            <div class="px-4 py-2 rounded-xl bg-white/5 text-xs font-black text-gray-400 uppercase tracking-widest">Last 50 Events</div>
        </div>

        <?php if (!$transactions): ?>
            <div class="py-12 text-center">
                <p class="text-gray-500 font-medium">No token activity recorded yet.</p>
            </div>
        <?php else: ?>
            <div class="overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/5 text-xs font-black text-gray-500 uppercase tracking-widest">
                            <th class="pb-4 pl-2">Description</th>
                            <th class="pb-4">Type</th>
                            <th class="pb-4">Amount</th>
                            <th class="pb-4 text-right pr-2">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($transactions as $tx): ?>
                            <tr class="group">
                                <td class="py-5 pl-2">
                                    <p class="text-white font-bold"><?php echo htmlspecialchars($tx['description'] ?: 'Token transaction'); ?></p>
                                    <p class="text-[10px] text-gray-600 font-mono mt-1 uppercase"><?php echo $tx['id']; ?></p>
                                </td>
                                <td class="py-5">
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-tighter <?php echo ($tx['type'] ?? 'debit') === 'credit' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'; ?>">
                                        <?php echo $tx['type'] ?? 'debit'; ?>
                                    </span>
                                </td>
                                <td class="py-5">
                                    <p class="font-black <?php echo ($tx['type'] ?? 'debit') === 'credit' ? 'text-emerald-400' : 'text-rose-400'; ?>">
                                        <?php echo ($tx['type'] ?? 'debit') === 'credit' ? '+' : '-'; ?><?php echo (int) $tx['amount']; ?>
                                    </p>
                                </td>
                                <td class="py-5 text-right pr-2 text-sm text-gray-500 font-medium">
                                    <?php echo date('M d, Y H:i', strtotime($tx['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>

<script>
const TOKEN_PRICE = 20;
const TOKEN_MIN = 50;
const tokenInput = document.getElementById('tokenInput');
const totalPrice = document.getElementById('totalPrice');

function updatePrice() {
    let val = parseInt(tokenInput.value) || 0;
    if (val < TOKEN_MIN) val = TOKEN_MIN;
    totalPrice.textContent = '₦' + (val * TOKEN_PRICE).toLocaleString();
}

tokenInput.addEventListener('input', updatePrice);
tokenInput.addEventListener('blur', () => {
    let val = parseInt(tokenInput.value) || 0;
    if (val < TOKEN_MIN) tokenInput.value = TOKEN_MIN;
    updatePrice();
});

document.getElementById('purchaseBtn').addEventListener('click', async function () {
    const tokens = parseInt(tokenInput.value) || 0;
    if (tokens < TOKEN_MIN) {
        document.getElementById('purchaseMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Minimum purchase is ' + TOKEN_MIN + ' tokens (₦' + (TOKEN_MIN * TOKEN_PRICE).toLocaleString() + ')</div>';
        return;
    }

    const btn = this;
    btn.disabled = true;
    btn.textContent = 'Processing...';

    const slug = '<?php echo $store['slug']; ?>';
    try {
        const res = await fetch(`/api/tokens_purchase.php?storeSlug=${slug}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ tokens })
        });
        const result = await res.json();
        if (result.success && result.authorization_url) {
            window.location.href = result.authorization_url;
        } else {
            document.getElementById('purchaseMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">' + (result.error || 'Failed to initiate payment') + '</div>';
            btn.disabled = false;
            btn.textContent = 'Buy Tokens';
        }
    } catch (err) {
        document.getElementById('purchaseMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Network error. Please try again.</div>';
        btn.disabled = false;
        btn.textContent = 'Buy Tokens';
    }
});
</script>

<?php
$content = ob_get_clean();
?>
