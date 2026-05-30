<?php
/*
 * Dashboard token management template.
 */

$pageTitle = 'Vomp Coin Management - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Order Credits</p>
            <h1 class="text-5xl font-black text-white tracking-tight mb-2">Vomp Coins</h1>
            <p class="text-gray-500 font-medium text-lg">Manage your order processing balance.</p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article class="glass-morphism rounded-[2.5rem] p-10 border border-white/10 md:col-span-1 flex flex-col items-center justify-center text-center">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-4">Current Balance</p>
            <div class="relative mb-6">
                <div class="absolute inset-0 bg-[#ff610a]/20 blur-3xl rounded-full"></div>
                <p class="text-7xl font-black text-white relative"><?php echo number_format((int) ($currentUser['token_balance'] ?? 0)); ?></p>
            </div>
            <p class="text-gray-400 text-sm font-medium">Vomp Coins available for <br>customer orders</p>
        </article>

        <div class="md:col-span-2 space-y-8">
            <div class="flex gap-2 mb-4 bg-white/5 rounded-xl p-1.5 w-fit">
                <button id="tabBuy" class="px-6 py-3 rounded-xl font-black text-sm transition-all bg-[#ff610a] text-white">Buy</button>
                <button id="tabTransfer" class="px-6 py-3 rounded-xl font-black text-sm transition-all bg-white/5 text-gray-400 hover:text-white">Transfer</button>
                <button id="tabWithdraw" class="px-6 py-3 rounded-xl font-black text-sm transition-all bg-white/5 text-gray-400 hover:text-white">Withdraw</button>
            </div>

            <!-- Buy Section -->
            <div id="buySection" class="glass-morphism rounded-3xl p-8 border border-white/10">
                <p class="text-xs uppercase tracking-[0.2em] font-black text-gray-500 mb-1">Price</p>
                <p class="text-3xl font-black text-white mb-6">₦20 <span class="text-sm text-gray-500 font-medium">per Vomp Coin</span></p>

                <div class="flex flex-col sm:flex-row gap-6 items-end">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Number of Vomp Coins</label>
                        <input type="number" id="tokenInput" min="50" step="1" value="50" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all text-lg font-black">
                        <p class="text-xs text-gray-500 mt-2 ml-1">Minimum: <span class="text-white font-bold">50 Vomp Coins</span> (₦1,000)</p>
                    </div>
                    <div class="w-full sm:w-48 text-center sm:text-right">
                        <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-1">Total Price</p>
                        <p id="totalPrice" class="text-3xl font-black text-[#ff610a]">₦1,000</p>
                    </div>
                </div>

                <button id="purchaseBtn" class="btn-press w-full py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all mt-8">
                    Buy Vomp Coins
                </button>
                <div id="purchaseMsg" class="mt-4"></div>
            </div>

            <!-- Transfer Section -->
            <div id="transferSection" class="glass-morphism rounded-3xl p-8 border border-white/10 hidden">
                <p class="text-xs uppercase tracking-[0.2em] font-black text-gray-500 mb-1">Send Vomp Coins</p>
                <p class="text-3xl font-black text-white mb-6">Transfer <span class="text-sm text-gray-500 font-medium">to another user</span></p>

                <div class="space-y-6">
                    <div class="w-full">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Recipient Email</label>
                        <input type="email" id="transferEmail" placeholder="user@example.com" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all text-lg font-black">
                    </div>
                    <div class="w-full">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Amount of Vomp Coins</label>
                        <input type="number" id="transferAmount" min="1" step="1" value="1" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all text-lg font-black">
                        <p class="text-xs text-gray-500 mt-2 ml-1">Your balance: <span id="transferBalance" class="text-white font-bold"><?php echo number_format((int) ($currentUser['token_balance'] ?? 0)); ?></span> Vomp Coins</p>
                    </div>
                </div>

                <button id="transferBtn" class="btn-press w-full py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all mt-8">
                    Transfer Vomp Coins
                </button>
                <div id="transferMsg" class="mt-4"></div>
            </div>

            <!-- Withdraw Section -->
            <div id="withdrawSection" class="glass-morphism rounded-3xl p-8 border border-white/10 hidden">
                <p class="text-xs uppercase tracking-[0.2em] font-black text-gray-500 mb-1">Cash Out</p>
                <p class="text-3xl font-black text-white mb-6">Withdraw <span class="text-sm text-gray-500 font-medium">to your bank account</span></p>

                <div class="space-y-6">
                    <div class="w-full">
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Vomp Coins to Withdraw</label>
                        <input type="number" id="withdrawAmount" min="50" step="1" value="50" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all text-lg font-black">
                        <p class="text-xs text-gray-500 mt-2 ml-1">Minimum: <span class="text-white font-bold">50 Vomp Coins</span> (₦1,000) &middot; Rate: ₦20 per Vomp Coin</p>
                    </div>
                    <div class="w-full">
                        <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-2 ml-1">You'll receive</p>
                        <p id="withdrawNaira" class="text-3xl font-black text-[#ff610a] ml-1">₦1,000</p>
                    </div>
                    <div class="border-t border-white/10 pt-6 space-y-6">
                        <div class="w-full">
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Bank</label>
                            <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet" />
                            <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
                            <select id="withdrawBank" placeholder="Search for your bank..." class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all text-lg font-black">
                                <option value="">Select your bank...</option>
                            </select>
                            <div id="withdrawBankLoading" class="text-xs text-gray-500 mt-2 ml-1">Loading banks...</div>
                        </div>
                        <div class="w-full">
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Account Number</label>
                            <div class="flex gap-3 items-end">
                                <div class="flex-1">
                                    <input type="text" id="withdrawAccount" maxlength="10" placeholder="0123456789" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all text-lg font-black" autocomplete="off">
                                </div>
                                <button id="withdrawVerifyBtn" class="px-6 py-4 rounded-2xl bg-[#ff610a] text-white font-black text-sm hover:bg-[#e05500] transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>Verify</button>
                            </div>
                            <div id="withdrawResolving" class="hidden mt-3 text-center py-3">
                                <div class="inline-block w-5 h-5 border-2 border-[#ff610a] border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-gray-400 text-xs mt-2 font-medium">Verifying account...</p>
                            </div>
                            <div id="withdrawAccountName" class="hidden mt-3 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-bold"></div>
                        </div>
                        <input type="hidden" id="withdrawBankCode" value="">
                        <p class="text-xs text-gray-500 ml-1">Your balance: <span id="withdrawBalance" class="text-white font-bold"><?php echo number_format((int) ($currentUser['token_balance'] ?? 0)); ?></span> Vomp Coins</p>
                    </div>
                </div>

                <button id="withdrawBtn" class="btn-press w-full py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all mt-8 hidden">
                    Submit Withdrawal Request
                </button>
                <div id="withdrawMsg" class="mt-4"></div>
            </div>
        </div>
    </div>

    <?php if (isset($transactions)): ?>
    <section class="glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-white">Transaction History</h2>
            <div class="px-4 py-2 rounded-xl bg-white/5 text-xs font-black text-gray-400 uppercase tracking-widest">Last 50 Events</div>
        </div>

        <?php if (!$transactions): ?>
            <div class="py-12 text-center">
                <p class="text-gray-500 font-medium">No Vomp Coin activity recorded yet.</p>
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
                                    <p class="text-white font-bold"><?php echo htmlspecialchars($tx['description'] ?: 'Vomp Coin transaction'); ?></p>
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
    <?php endif; ?>
</section>

<script>
const TOKEN_PRICE = 20;
const TOKEN_MIN = 50;
const tokenInput = document.getElementById('tokenInput');
const totalPrice = document.getElementById('totalPrice');
const purchaseBtn = document.getElementById('purchaseBtn');
const tabBuy = document.getElementById('tabBuy');
const tabTransfer = document.getElementById('tabTransfer');
const tabWithdraw = document.getElementById('tabWithdraw');
const buySection = document.getElementById('buySection');
const transferSection = document.getElementById('transferSection');
const withdrawSection = document.getElementById('withdrawSection');
const transferBtn = document.getElementById('transferBtn');
const withdrawBtn = document.getElementById('withdrawBtn');
const withdrawAmount = document.getElementById('withdrawAmount');
const withdrawNaira = document.getElementById('withdrawNaira');

function switchTab(tab) {
    const active = 'bg-[#ff610a] text-white';
    const inactive = 'bg-white/5 text-gray-400 hover:text-white';
    tabBuy.className = 'px-6 py-3 rounded-xl font-black text-sm transition-all ' + (tab === 'buy' ? active : inactive);
    tabTransfer.className = 'px-6 py-3 rounded-xl font-black text-sm transition-all ' + (tab === 'transfer' ? active : inactive);
    tabWithdraw.className = 'px-6 py-3 rounded-xl font-black text-sm transition-all ' + (tab === 'withdraw' ? active : inactive);
    buySection.classList.toggle('hidden', tab !== 'buy');
    transferSection.classList.toggle('hidden', tab !== 'transfer');
    withdrawSection.classList.toggle('hidden', tab !== 'withdraw');
}

tabBuy.addEventListener('click', () => switchTab('buy'));
tabTransfer.addEventListener('click', () => switchTab('transfer'));
tabWithdraw.addEventListener('click', () => switchTab('withdraw'));

function updateWithdrawNaira() {
    let val = parseInt(withdrawAmount.value) || 0;
    if (val < 0) val = 0;
    withdrawNaira.textContent = '₦' + (val * TOKEN_PRICE).toLocaleString();
}
withdrawAmount.addEventListener('input', updateWithdrawNaira);

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

if (purchaseBtn) {
    purchaseBtn.addEventListener('click', async function () {
        const tokens = parseInt(tokenInput.value) || 0;
        if (tokens < TOKEN_MIN) {
            document.getElementById('purchaseMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Minimum purchase is ' + TOKEN_MIN + ' Vomp Coins (₦' + (TOKEN_MIN * TOKEN_PRICE).toLocaleString() + ')</div>';
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Processing...';

        <?php if (isset($store) && $store): ?>
        const slug = '<?php echo $store['slug']; ?>';
        var url = '/api/tokens_purchase.php?storeSlug=' + slug;
        <?php else: ?>
        var url = '/api/tokens_purchase.php';
        <?php endif; ?>

        try {
            const res = await fetch(url, {
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
                btn.textContent = 'Buy Vomp Coins';
            }
        } catch (err) {
            document.getElementById('purchaseMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Network error. Please try again.</div>';
            btn.disabled = false;
            btn.textContent = 'Buy Vomp Coins';
        }
    });
}

if (transferBtn) {
    transferBtn.addEventListener('click', async function () {
        const email = document.getElementById('transferEmail').value.trim();
        const amount = parseInt(document.getElementById('transferAmount').value) || 0;

        if (!email) {
            document.getElementById('transferMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Please enter the recipient\'s email address</div>';
            return;
        }

        if (amount < 1) {
            document.getElementById('transferMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Amount must be at least 1 Vomp Coin</div>';
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Processing...';

        try {
            const res = await fetch('/api/tokens_transfer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, amount })
            });
            const result = await res.json();
            if (result.success) {
                document.getElementById('transferMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-bold">Successfully transferred ' + amount + ' Vomp Coins to ' + email + '</div>';
                document.getElementById('transferBalance').textContent = result.token_balance.toLocaleString();
                document.getElementById('transferAmount').value = 1;
            } else {
                document.getElementById('transferMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">' + (result.error || 'Transfer failed') + '</div>';
            }
        } catch (err) {
            document.getElementById('transferMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Network error. Please try again.</div>';
        }

        btn.disabled = false;
        btn.textContent = 'Transfer Vomp Coins';
    });
}

let withdrawBankCode = '';
let withdrawBankName = '';
let withdrawAccountName = '';
let withdrawTomSelect = null;

// Load banks into dropdown
fetch('/api/list_banks.php')
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('withdrawBank');
        document.getElementById('withdrawBankLoading').classList.add('hidden');
        if (data.success && data.banks.length > 0) {
            data.banks.sort((a, b) => a.name.localeCompare(b.name));
            data.banks.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.code;
                opt.textContent = b.name;
                select.appendChild(opt);
            });
        }
        // Init Tom Select after options loaded
        withdrawTomSelect = new TomSelect('#withdrawBank', {
            placeholder: 'Search for your bank...',
            allowEmptyOption: true,
            maxOptions: 100,
            searchField: ['text'],
            onChange: function (value) {
                withdrawBankCode = value;
                const opt = this.options[value];
                withdrawBankName = opt ? opt.text : '';
                document.getElementById('withdrawAccountName').classList.add('hidden');
                document.getElementById('withdrawBtn').classList.add('hidden');
                checkVerifyReady();
            }
        });
    })
    .catch(() => {
        document.getElementById('withdrawBankLoading').textContent = 'Failed to load banks';
    });

// Enable verify button when bank + account are filled
function checkVerifyReady() {
    const bank = document.getElementById('withdrawBank').value;
    const acct = document.getElementById('withdrawAccount').value.replace(/\D/g, '');
    document.getElementById('withdrawVerifyBtn').disabled = !(bank && acct.length === 10);
}

document.getElementById('withdrawAccount').addEventListener('input', function () {
    const num = this.value.replace(/\D/g, '').slice(0, 10);
    this.value = num;
    document.getElementById('withdrawAccountName').classList.add('hidden');
    document.getElementById('withdrawBtn').classList.add('hidden');
    checkVerifyReady();
});

// Verify account
document.getElementById('withdrawVerifyBtn').addEventListener('click', async function () {
    const bankCode = document.getElementById('withdrawBank').value;
    const acct = document.getElementById('withdrawAccount').value.replace(/\D/g, '');
    if (!bankCode || acct.length !== 10) return;

    const resolving = document.getElementById('withdrawResolving');
    const nameEl = document.getElementById('withdrawAccountName');
    resolving.classList.remove('hidden');
    nameEl.classList.add('hidden');
    document.getElementById('withdrawBtn').classList.add('hidden');

    try {
        const res = await fetch('/api/resolve_account.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ account_number: acct, bank_code: bankCode })
        });
        const data = await res.json();
        resolving.classList.add('hidden');
        if (data.success && data.results.length > 0) {
            const r = data.results[0];
            withdrawAccountName = r.account_name;
            nameEl.textContent = '✓ ' + r.account_name;
            nameEl.className = 'mt-3 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-bold';
            nameEl.classList.remove('hidden');
            document.getElementById('withdrawBtn').classList.remove('hidden');
        } else {
            nameEl.textContent = '✗ Account not found for this bank';
            nameEl.className = 'mt-3 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold';
            nameEl.classList.remove('hidden');
        }
    } catch (err) {
        resolving.classList.add('hidden');
        document.getElementById('withdrawAccountName').textContent = 'Network error. Try again.';
        document.getElementById('withdrawAccountName').className = 'mt-3 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold';
        document.getElementById('withdrawAccountName').classList.remove('hidden');
    }
});

if (withdrawBtn) {
    withdrawBtn.addEventListener('click', async function () {
        const amount = parseInt(withdrawAmount.value) || 0;
        const accountNumber = document.getElementById('withdrawAccount').value.trim();

        if (amount < 50) {
            document.getElementById('withdrawMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Minimum withdrawal is 50 Vomp Coins (₦1,000)</div>';
            return;
        }
        if (!withdrawBankCode) {
            document.getElementById('withdrawMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Please select a bank and verify your account</div>';
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.textContent = 'Processing...';

        try {
            const res = await fetch('/api/tokens_withdraw.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount,
                    bank_name: withdrawBankName,
                    bank_code: withdrawBankCode,
                    account_number: accountNumber,
                    account_name: withdrawAccountName
                })
            });
            const result = await res.json();
            if (result.success) {
                document.getElementById('withdrawMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-bold">Withdrawal successful! ' + amount + ' Vomp Coins (₦' + (amount * TOKEN_PRICE).toLocaleString() + ') sent to ' + withdrawBankName + ' ' + accountNumber + ' (' + withdrawAccountName + ')</div>';
                document.getElementById('withdrawBalance').textContent = result.token_balance.toLocaleString();
                document.getElementById('withdrawAmount').value = 50;
                updateWithdrawNaira();
                document.getElementById('withdrawAccount').value = '';
                document.getElementById('withdrawAccountName').classList.add('hidden');
                document.getElementById('withdrawBtn').classList.add('hidden');
                document.getElementById('withdrawAccount').dispatchEvent(new Event('input'));
            } else {
                document.getElementById('withdrawMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">' + (result.error || 'Withdrawal failed') + '</div>';
            }
        } catch (err) {
            document.getElementById('withdrawMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Network error. Please try again.</div>';
        }

        btn.disabled = false;
        btn.textContent = 'Submit Withdrawal Request';
    });
}
</script>

<?php
$content = ob_get_clean();
?>
