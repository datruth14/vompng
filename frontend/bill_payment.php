<?php
$pageTitle = 'Bill Payment - vomp';
$userBalance = $currentUser ? token_user_balance($currentUser['id']) : 0;
$coinsWorth = $userBalance * TOKEN_PRICE_PER_UNIT;
ob_start();
?>
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet" />
<style>
.ts-wrapper .ts-control {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    color: #fff;
    font-size: 0.875rem;
    box-shadow: none;
}
.ts-wrapper .ts-control:hover {
    border-color: rgba(255,255,255,0.15);
}
.ts-wrapper.focus .ts-control {
    border-color: rgba(255,97,10,0.5);
    box-shadow: none;
}
.ts-wrapper .ts-control input {
    color: #fff;
}
.ts-wrapper .ts-control .item {
    color: #fff;
}
.ts-dropdown {
    background: #1a1a2e;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 0.75rem;
    color: #fff;
    z-index: 9999;
}
.ts-dropdown .option {
    color: #ccc;
    padding: 0.5rem 1rem;
}
.ts-dropdown .option.active {
    background: rgba(255,97,10,0.2);
    color: #fff;
}
.ts-dropdown .option.highlight {
    background: rgba(255,97,10,0.3);
    color: #fff;
}
.ts-dropdown .no-results {
    color: #666;
    padding: 0.5rem 1rem;
}
.ts-wrapper .ts-control .dropdown-active {
    border-color: rgba(255,97,10,0.5);
}
.ts-wrapper .ts-control .dropdown-menu {
    background: #1a1a2e;
}
</style>
<section class="py-12">
    <div class="max-w-4xl mx-auto text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4 animate__animated animate__fadeInDown">Bill Payment</h1>
        <p class="text-gray-400 text-lg animate__animated animate__fadeInUp">Pay for airtime, data, electricity, TV, betting, and ePINs instantly with Vomp Coins.</p>
    </div>

    <!-- Balance Card -->
    <div class="max-w-xs mx-auto mb-12 animate__animated animate__bounceIn">
        <div class="glass-morphism rounded-2xl p-5 border border-white/10 text-center">
            <p class="text-gray-500 text-xs uppercase tracking-widest font-bold mb-2">Your Balance</p>
            <p id="vcBalanceDisplay" class="text-3xl md:text-4xl font-black text-white text-fit"><?= number_format($userBalance) ?> <span class="text-[#ff610a] text-xl">VC</span></p>
            <p class="text-gray-500 text-xs mt-1">≈ ₦<?= number_format($coinsWorth) ?></p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-16">
        <!-- Airtime -->
        <button onclick="openBillModal('airtime')" class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 text-center hover:bg-white/[0.06] transition-all group animate__animated animate__fadeInUp">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-orange-500 to-rose-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
            </div>
            <p class="text-white font-black text-sm md:text-base">Airtime</p>
            <p class="text-gray-500 text-xs mt-1">MTN, Glo, Airtel, 9mobile</p>
        </button>

        <!-- Data -->
        <button onclick="openBillModal('data')" class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 text-center hover:bg-white/[0.06] transition-all group animate__animated animate__fadeInUp" style="animation-delay:0.1s">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>
            </div>
            <p class="text-white font-black text-sm md:text-base">Data Bundle</p>
            <p class="text-gray-500 text-xs mt-1">MTN, Glo, Airtel, 9mobile</p>
        </button>

        <!-- Electricity -->
        <button onclick="openBillModal('electricity')" class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 text-center hover:bg-white/[0.06] transition-all group animate__animated animate__fadeInUp" style="animation-delay:0.2s">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg>
            </div>
            <p class="text-white font-black text-sm md:text-base">Electricity</p>
            <p class="text-gray-500 text-xs mt-1">IKEDC, AEDC, EKEDC, PHED</p>
        </button>

        <!-- Cable TV -->
        <button onclick="openBillModal('tv')" class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 text-center hover:bg-white/[0.06] transition-all group animate__animated animate__fadeInUp" style="animation-delay:0.3s">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>
            </div>
            <p class="text-white font-black text-sm md:text-base">Cable TV</p>
            <p class="text-gray-500 text-xs mt-1">DSTV, GOtv, Showmax</p>
        </button>

        <!-- Betting -->
        <button onclick="openBillModal('betting')" class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 text-center hover:bg-white/[0.06] transition-all group animate__animated animate__fadeInUp" style="animation-delay:0.35s">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" /></svg>
            </div>
            <p class="text-white font-black text-sm md:text-base">Betting</p>
            <p class="text-gray-500 text-xs mt-1">Bet9ja, BetKing, 1xBet</p>
        </button>

        <!-- ePINs -->
        <button onclick="openBillModal('epins')" class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 text-center hover:bg-white/[0.06] transition-all group animate__animated animate__fadeInUp" style="animation-delay:0.4s">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
            </div>
            <p class="text-white font-black text-sm md:text-base">ePINs</p>
            <p class="text-gray-500 text-xs mt-1">Recharge card PINs</p>
        </button>
    </div>

    <!-- Bill Modal -->
    <div id="billModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 max-w-md w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 id="billModalTitle" class="text-white font-black text-xl">Select Service</h2>
                <button onclick="closeBillModal()" class="p-2 rounded-xl bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Coin cost indicator -->
            <div id="coinCostBar" class="mb-5 px-4 py-3 rounded-xl bg-white/5 border border-white/10 hidden">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Cost:</span>
                    <span id="coinCostDisplay" class="text-white font-bold text-lg">0 <span class="text-[#ff610a] text-sm">VC</span></span>
                </div>
            </div>

            <div id="billModalBody" class="space-y-3">
                <!-- Dynamically populated -->
            </div>

            <!-- Feedback -->
            <div id="billFeedback" class="mt-4 hidden"></div>

            <p class="text-center text-xs text-gray-600 mt-4">Powered by VomP &amp; VTU.NG</p>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<!-- PIN Modal -->
<div id="pinModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
    <div class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 max-w-sm w-full mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h2 id="pinModalTitle" class="text-white font-black text-xl">Enter Transaction PIN</h2>
            <button onclick="closePinModal()" class="p-2 rounded-xl bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div id="pinModalBody">
            <p id="pinModalDesc" class="text-gray-400 text-sm mb-5">Enter your 4-digit transaction PIN to continue.</p>
            <div class="mb-4">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Transaction PIN</label>
                <input type="password" id="pinInput" maxlength="4" inputmode="numeric" pattern="[0-9]*" autocomplete="off" placeholder="••••" class="w-full text-center text-2xl tracking-[0.5em] bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all">
            </div>
            <div id="pinConfirmGroup" class="mb-4 hidden">
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Confirm PIN</label>
                <input type="password" id="pinConfirmInput" maxlength="4" inputmode="numeric" pattern="[0-9]*" autocomplete="off" placeholder="••••" class="w-full text-center text-2xl tracking-[0.5em] bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all">
            </div>
        </div>
        <div id="pinFeedback" class="mb-4 hidden"></div>
        <button id="pinSubmitBtn" class="btn-press w-full py-4 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">Continue</button>
    </div>
</div>

<script>
var PENDING_PAYLOAD = null;

function openPinModal() {
    document.getElementById('pinInput').value = '';
    document.getElementById('pinConfirmInput').value = '';
    document.getElementById('pinFeedback').classList.add('hidden');
    document.getElementById('pinConfirmGroup').classList.add('hidden');
    document.getElementById('pinModalTitle').textContent = 'Enter Transaction PIN';
    document.getElementById('pinModalDesc').textContent = 'Enter your 4-digit transaction PIN to continue.';
    document.getElementById('pinSubmitBtn').textContent = 'Verify PIN';
    document.getElementById('pinModal').classList.remove('hidden');
    document.getElementById('pinInput').focus();
}

function closePinModal() {
    document.getElementById('pinModal').classList.add('hidden');
}

document.getElementById('pinSubmitBtn').addEventListener('click', async function() {
    var pin = document.getElementById('pinInput').value.trim();
    if (!pin || pin.length !== 4 || !/^\d{4}$/.test(pin)) {
        document.getElementById('pinFeedback').className = 'mb-4 px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold';
        document.getElementById('pinFeedback').textContent = 'Enter a valid 4-digit PIN';
        document.getElementById('pinFeedback').classList.remove('hidden');
        return;
    }

    var btn = this;
    btn.disabled = true;
    btn.textContent = 'Verifying...';

    try {
        var res = await fetch('/api/verify_pin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pin: pin })
        });
        var data = await res.json();

        if (data.success) {
            closePinModal();
            executeBillPayment(pin);
            return;
        }

        if (data.setup_required) {
            document.getElementById('pinModalTitle').textContent = 'Set Transaction PIN';
            document.getElementById('pinModalDesc').textContent = 'You need to set a 4-digit transaction PIN before proceeding.';
            document.getElementById('pinConfirmGroup').classList.remove('hidden');
            document.getElementById('pinSubmitBtn').textContent = 'Set PIN';
            document.getElementById('pinFeedback').className = 'mb-4 px-4 py-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-sm font-bold';
            document.getElementById('pinFeedback').textContent = 'No PIN set. Create one now.';
            document.getElementById('pinFeedback').classList.remove('hidden');

            btn.onclick = async function() {
                var newPin = document.getElementById('pinInput').value.trim();
                var confirmPin = document.getElementById('pinConfirmInput').value.trim();
                if (!newPin || newPin.length !== 4 || !/^\d{4}$/.test(newPin)) {
                    document.getElementById('pinFeedback').textContent = 'Enter a valid 4-digit PIN';
                    return;
                }
                if (newPin !== confirmPin) {
                    document.getElementById('pinFeedback').textContent = 'PINs do not match';
                    return;
                }
                btn.disabled = true;
                btn.textContent = 'Setting PIN...';
                try {
                    var setRes = await fetch('/api/set_pin.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ pin: newPin })
                    });
                    var setData = await setRes.json();
                    if (setData.success) {
                        closePinModal();
                        executeBillPayment(newPin);
                    } else {
                        document.getElementById('pinFeedback').textContent = setData.error || 'Failed to set PIN';
                        btn.disabled = false;
                        btn.textContent = 'Set PIN';
                    }
                } catch(e) {
                    document.getElementById('pinFeedback').textContent = 'Network error. Try again.';
                    btn.disabled = false;
                    btn.textContent = 'Set PIN';
                }
            };
            btn.disabled = false;
            btn.textContent = 'Set PIN';
            return;
        }

        document.getElementById('pinFeedback').className = 'mb-4 px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold';
        document.getElementById('pinFeedback').textContent = data.error || 'Invalid PIN';
        document.getElementById('pinFeedback').classList.remove('hidden');
    } catch(e) {
        document.getElementById('pinFeedback').className = 'mb-4 px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold';
        document.getElementById('pinFeedback').textContent = 'Network error. Try again.';
        document.getElementById('pinFeedback').classList.remove('hidden');
    }

    btn.disabled = false;
    btn.textContent = 'Verify PIN';
});

var USER_BALANCE = <?= $userBalance ?>;
var TOKEN_PRICE = <?= TOKEN_PRICE_PER_UNIT ?>;
var billTomSelects = [];

var billServices = {
    airtime: {
        title: 'Buy Airtime',
        type: 'airtime',
        getAmount: function(fields) { return parseInt(fields.amount) || 0; },
        getCustomerId: function(fields) { return fields.phone || ''; },
        fields: [
            { name: 'service_id', label: 'Network', type: 'select', tomSelect: true, options: [
                { label: 'MTN', value: 'mtn' },
                { label: 'Glo', value: 'glo' },
                { label: 'Airtel', value: 'airtel' },
                { label: '9mobile', value: '9mobile' }
            ]},
            { name: 'phone', label: 'Phone Number', type: 'tel', placeholder: '08012345678' },
            { name: 'amount', label: 'Amount (₦)', type: 'number', inputmode: 'numeric', placeholder: '100' }
        ]
    },
    data: {
        title: 'Buy Data Bundle',
        type: 'data',
        getAmount: function(fields) { return parseInt(fields.amount) || 0; },
        getCustomerId: function(fields) { return fields.phone || ''; },
        onNetworkChange: function(serviceId, selectEl) {
            var container = selectEl.closest('.modal-fields');
            var variationSelect = container.querySelector('[data-name="variation_id"]');
            if (!variationSelect) return;
            destroyTomSelect(variationSelect);
            variationSelect.innerHTML = '<option value="">Loading plans...</option>';
            initSingleTomSelect(variationSelect);
            fetch('/api/bill_variations.php?type=data&service_id=' + encodeURIComponent(serviceId))
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (!res.success || !res.data || !res.data.length) {
                        destroyTomSelect(variationSelect);
                        variationSelect.innerHTML = '<option value="">No plans available</option>';
                        initSingleTomSelect(variationSelect);
                        return;
                    }
                    destroyTomSelect(variationSelect);
                    variationSelect.innerHTML = '';
                    res.data.forEach(function(v) {
                        if (v.availability === 'Unavailable') return;
                        var opt = document.createElement('option');
                        opt.value = v.variation_id;
                        opt.textContent = v.data_plan + ' - ₦' + v.price;
                        opt.setAttribute('data-price', v.price);
                        variationSelect.appendChild(opt);
                    });
                    if (variationSelect.options.length === 0) {
                        variationSelect.innerHTML = '<option value="">No plans available</option>';
                    }
                    initSingleTomSelect(variationSelect);
                    variationSelect.dispatchEvent(new Event('change'));
                })
                .catch(function() {
                    destroyTomSelect(variationSelect);
                    variationSelect.innerHTML = '<option value="">Failed to load plans</option>';
                    initSingleTomSelect(variationSelect);
                });
        },
        fields: [
            { name: 'service_id', label: 'Network', type: 'select', tomSelect: true, options: [
                { label: 'MTN', value: 'mtn' },
                { label: 'Glo', value: 'glo' },
                { label: 'Airtel', value: 'airtel' },
                { label: '9mobile', value: '9mobile' }
            ]},
            { name: 'phone', label: 'Phone Number', type: 'tel', placeholder: '08012345678' },
            { name: 'variation_id', label: 'Data Plan', type: 'select', tomSelect: true, options: [
                { label: 'Select a network first...', value: '' }
            ]}
        ]
    },
    electricity: {
        title: 'Pay Electricity Bill',
        type: 'electricity',
        getAmount: function(fields) { return parseInt(fields.amount) || 0; },
        getCustomerId: function(fields) { return fields.customer_id || ''; },
        fields: [
            { name: 'service_id', label: 'Disco', type: 'select', tomSelect: true, options: [
                { label: 'IKEDC (Ikeja)', value: 'ikeja-electric' },
                { label: 'EKEDC (Eko)', value: 'eko-electric' },
                { label: 'AEDC (Abuja)', value: 'abuja-electric' },
                { label: 'IBEDC (Ibadan)', value: 'ibadan-electric' },
                { label: 'PHED (Port Harcourt)', value: 'portharcourt-electric' },
                { label: 'KAEDCO (Kaduna)', value: 'kaduna-electric' },
                { label: 'JED (Jos)', value: 'jos-electric' },
                { label: 'KEDCO (Kano)', value: 'kano-electric' },
                { label: 'EEDC (Enugu)', value: 'enugu-electric' },
                { label: 'BEDC (Benin)', value: 'benin-electric' },
                { label: 'ABEDC (Aba)', value: 'aba-electric' },
                { label: 'YEDC (Yola)', value: 'yola-electric' }
            ]},
            { name: 'customer_id', label: 'Meter Number', type: 'text', placeholder: 'Enter meter number' },
            { name: 'variation_id', label: 'Meter Type', type: 'select', tomSelect: true, options: [
                { label: 'Prepaid', value: 'prepaid' },
                { label: 'Postpaid', value: 'postpaid' }
            ]},
            { name: 'amount', label: 'Amount (₦)', type: 'number', inputmode: 'numeric', placeholder: '1000' }
        ]
    },
    tv: {
        title: 'Pay Cable TV',
        type: 'tv',
        getAmount: function(fields) { return parseInt(fields.amount) || 0; },
        getCustomerId: function(fields) { return fields.customer_id || ''; },
        onNetworkChange: function(serviceId, selectEl) {
            var container = selectEl.closest('.modal-fields');
            var variationSelect = container.querySelector('[data-name="variation_id"]');
            if (!variationSelect) return;
            destroyTomSelect(variationSelect);
            variationSelect.innerHTML = '<option value="">Loading packages...</option>';
            initSingleTomSelect(variationSelect);
            fetch('/api/bill_variations.php?type=tv&service_id=' + encodeURIComponent(serviceId))
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (!res.success || !res.data || !res.data.length) {
                        destroyTomSelect(variationSelect);
                        variationSelect.innerHTML = '<option value="">No packages available</option>';
                        initSingleTomSelect(variationSelect);
                        return;
                    }
                    destroyTomSelect(variationSelect);
                    variationSelect.innerHTML = '';
                    res.data.forEach(function(v) {
                        if (v.availability === 'Unavailable') return;
                        var opt = document.createElement('option');
                        opt.value = v.variation_id;
                        opt.textContent = v.package_bouquet + ' - ₦' + v.price;
                        opt.setAttribute('data-price', v.price);
                        variationSelect.appendChild(opt);
                    });
                    if (variationSelect.options.length === 0) {
                        variationSelect.innerHTML = '<option value="">No packages available</option>';
                    }
                    initSingleTomSelect(variationSelect);
                    variationSelect.dispatchEvent(new Event('change'));
                })
                .catch(function() {
                    destroyTomSelect(variationSelect);
                    variationSelect.innerHTML = '<option value="">Failed to load packages</option>';
                    initSingleTomSelect(variationSelect);
                });
        },
        fields: [
            { name: 'service_id', label: 'Provider', type: 'select', tomSelect: true, options: [
                { label: 'DSTV', value: 'dstv' },
                { label: 'GOtv', value: 'gotv' },
                { label: 'Startimes', value: 'startimes' },
                { label: 'Showmax', value: 'showmax' }
            ]},
            { name: 'customer_id', label: 'Smart Card / IUC Number', type: 'text', placeholder: 'Enter smart card number' },
            { name: 'variation_id', label: 'Package', type: 'select', tomSelect: true, options: [
                { label: 'Select a provider first...', value: '' }
            ]}
        ]
    },
    betting: {
        title: 'Fund Betting Account',
        type: 'betting',
        getAmount: function(fields) { return parseInt(fields.amount) || 0; },
        getCustomerId: function(fields) { return fields.customer_id || ''; },
        fields: [
            { name: 'service_id', label: 'Provider', type: 'select', tomSelect: true, options: [
                { label: 'Bet9ja', value: 'Bet9ja' },
                { label: 'BetKing', value: 'BetKing' },
                { label: '1xBet', value: '1xBet' },
                { label: 'NairaBet', value: 'NairaBet' },
                { label: 'NaijaBet', value: 'NaijaBet' },
                { label: 'MerryBet', value: 'MerryBet' },
                { label: 'BetLion', value: 'BetLion' },
                { label: 'BetWay', value: 'BetWay' },
                { label: 'LiveScoreBet', value: 'LiveScoreBet' },
                { label: 'SupaBet', value: 'SupaBet' },
                { label: 'CloudBet', value: 'CloudBet' },
                { label: 'BangBet', value: 'BangBet' },
                { label: 'BetLand', value: 'BetLand' }
            ]},
            { name: 'customer_id', label: 'Betting ID / Username', type: 'text', placeholder: 'Enter your betting account ID' },
            { name: 'amount', label: 'Amount (₦)', type: 'number', inputmode: 'numeric', placeholder: '500' }
        ]
    },
    epins: {
        title: 'Buy ePINs',
        type: 'epins',
        getAmount: function(fields) {
            var val = parseInt(fields.value) || 0;
            var qty = parseInt(fields.quantity) || 1;
            return val * qty;
        },
        getCustomerId: function(fields) { return 'qty_' + (fields.quantity || 1); },
        fields: [
            { name: 'service_id', label: 'Network', type: 'select', tomSelect: true, options: [
                { label: 'MTN', value: 'mtn' },
                { label: 'Glo', value: 'glo' },
                { label: 'Airtel', value: 'airtel' },
                { label: '9mobile', value: '9mobile' }
            ]},
            { name: 'value', label: 'Denomination', type: 'select', tomSelect: true, options: [
                { label: '₦100', value: '100' },
                { label: '₦200', value: '200' },
                { label: '₦500', value: '500' }
            ]},
            { name: 'quantity', label: 'Quantity', type: 'number', inputmode: 'numeric', placeholder: '1' }
        ]
    }
};

function destroyTomSelect(el) {
    if (el && el.tomselect) {
        el.tomselect.destroy();
        el.tomselect = null;
    }
}

function initSingleTomSelect(el) {
    if (!el || el.tomselect) return;
    new TomSelect(el, {
        placeholder: 'Search...',
        allowEmptyOption: true,
        maxOptions: null,
        maxItems: 1,
        searchField: ['text'],
        onChange: function() { updateCoinCost(billServices[lastModalType]); }
    });
}

var lastModalType = null;

function openBillModal(type) {
    lastModalType = type;
    var svc = billServices[type];
    if (!svc) return;
    document.getElementById('billModalTitle').textContent = svc.title;
    var body = document.getElementById('billModalBody');
    body.innerHTML = '';
    var container = document.createElement('div');
    container.className = 'modal-fields';

    svc.fields.forEach(function(f) {
        var wrapper = document.createElement('div');
        wrapper.className = 'mb-4';
        var label = document.createElement('label');
        label.className = 'block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1';
        label.textContent = f.label;
        wrapper.appendChild(label);
        var el;
        if (f.type === 'select') {
            el = document.createElement('select');
            el.className = 'w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#ff610a]/50 transition-all';
            f.options.forEach(function(o) {
                var opt = document.createElement('option');
                opt.value = o.value || o;
                opt.textContent = o.label || o;
                if (o.price) opt.setAttribute('data-price', o.price);
                el.appendChild(opt);
            });
        } else {
            el = document.createElement('input');
            el.type = f.type;
            el.inputMode = f.inputmode || 'text';
            el.className = 'w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all';
            if (f.placeholder) el.placeholder = f.placeholder;
        }
        if (f.name) el.setAttribute('data-name', f.name);
        wrapper.appendChild(el);
        container.appendChild(wrapper);

        if (f.name === 'service_id' && svc.onNetworkChange) {
            el.addEventListener('change', function() {
                svc.onNetworkChange(this.value, this);
                updateCoinCost(svc);
            });
        }
        el.addEventListener('change', function() { updateCoinCost(svc); });
        el.addEventListener('input', function() { updateCoinCost(svc); });
    });

    body.appendChild(container);

    // Initialize TomSelect on all select elements with tomSelect: true
    svc.fields.forEach(function(f, idx) {
        if (f.type === 'select' && f.tomSelect) {
            var selectEl = container.querySelectorAll('select[data-name]')[idx];
            if (selectEl) {
                setTimeout(function() {
                    initSingleTomSelect(selectEl);
                }, 50);
            }
        }
    });

    // Proceed button
    var btnWrap = document.createElement('div');
    btnWrap.className = 'mt-6';
    var btn = document.createElement('button');
    btn.className = 'btn-press w-full py-4 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all submit-btn';
    btn.textContent = 'Proceed';
    btn.onclick = function() { proceedBill(svc); };
    btnWrap.appendChild(btn);
    body.appendChild(btnWrap);

    document.getElementById('billModal').classList.remove('hidden');
    updateCoinCost(svc);

    // Auto-fetch variations if network/provider is pre-selected
    var netSelect = container.querySelector('[data-name="service_id"]');
    if (netSelect && netSelect.value && svc.onNetworkChange) {
        svc.onNetworkChange(netSelect.value, netSelect);
    }
}

function getFieldValues(svc) {
    var fields = {};
    var container = document.querySelector('.modal-fields');
    if (!container) return fields;
    container.querySelectorAll('[data-name]').forEach(function(el) {
        fields[el.getAttribute('data-name')] = el.value;
    });
    return fields;
}

function updateCoinCost(svc) {
    var fields = getFieldValues(svc);
    var nairaAmount = svc.getAmount ? svc.getAmount(fields) : 0;

    var container = document.querySelector('.modal-fields');
    if (container) {
        container.querySelectorAll('select[data-name]').forEach(function(sel) {
            var selected = sel.options[sel.selectedIndex];
            if (selected && selected.getAttribute('data-price')) {
                var price = parseInt(selected.getAttribute('data-price'));
                if (price > 0) {
                    var name = sel.getAttribute('data-name');
                    if (name === 'variation_id') {
                        nairaAmount = price;
                    }
                }
            }
        });
    }

    var coins = nairaAmount > 0 ? Math.ceil(nairaAmount / TOKEN_PRICE) : 0;
    var bar = document.getElementById('coinCostBar');
    var display = document.getElementById('coinCostDisplay');
    if (coins > 0) {
        bar.classList.remove('hidden');
        display.innerHTML = coins + ' <span class="text-[#ff610a] text-sm">VC</span> <span class="text-gray-500 text-sm font-normal">(₦' + nairaAmount.toLocaleString() + ')</span>';
    } else {
        bar.classList.add('hidden');
    }
}

function proceedBill(svc) {
    var fields = getFieldValues(svc);
    var nairaAmount = svc.getAmount ? svc.getAmount(fields) : 0;

    // Close any open TomSelect dropdowns
    document.querySelectorAll('.ts-dropdown').forEach(function(d) { d.style.display = 'none'; });

    var container = document.querySelector('.modal-fields');
    if (container) {
        container.querySelectorAll('select[data-name]').forEach(function(sel) {
            var selected = sel.options[sel.selectedIndex];
            if (selected && selected.getAttribute('data-price')) {
                var price = parseInt(selected.getAttribute('data-price'));
                if (price > 0 && sel.getAttribute('data-name') === 'variation_id') {
                    nairaAmount = price;
                }
            }
        });
    }

    var coins = Math.ceil(nairaAmount / TOKEN_PRICE);

    if (!nairaAmount || nairaAmount <= 0) {
        showFeedback('Please fill in all required fields.', 'error');
        return;
    }

    if (USER_BALANCE < coins) {
        showFeedback('Insufficient Vomp Coins. You need ' + coins + ' VC but have ' + USER_BALANCE + ' VC. <a href="/tokens" class="underline text-[#ff610a]">Buy more</a>', 'error');
        return;
    }

    var payload = { type: svc.type };
    Object.keys(fields).forEach(function(k) { payload[k] = fields[k]; });
    payload.amount = nairaAmount;
    PENDING_PAYLOAD = payload;
    openPinModal();
}

function executeBillPayment(pin) {
    var payload = PENDING_PAYLOAD || {};
    PENDING_PAYLOAD = null;
    payload.pin = pin;

    var btn = document.querySelector('.submit-btn');
    btn.disabled = true;
    btn.textContent = 'Processing...';
    showFeedback('', '');

    fetch('/api/bill_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.textContent = 'Proceed';
        if (res.success) {
            USER_BALANCE = res.new_balance || (USER_BALANCE - Math.ceil((payload.amount || 0) / TOKEN_PRICE));
            showFeedback('✅ ' + (res.message || 'Payment successful!'), 'success');
            setTimeout(function() {
                closeBillModal();
                location.reload();
            }, 1500);
        } else {
            showFeedback('❌ ' + (res.error || 'Payment failed. Please try again.'), 'error');
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.textContent = 'Proceed';
        showFeedback('❌ Network error. Please try again.', 'error');
    });
}

function showFeedback(msg, type) {
    var el = document.getElementById('billFeedback');
    if (!msg) { el.classList.add('hidden'); el.innerHTML = ''; return; }
    el.classList.remove('hidden');
    el.className = 'mt-4 px-4 py-3 rounded-xl text-sm font-medium ' + (type === 'success' ? 'bg-green-500/10 border border-green-500/20 text-green-400' : 'bg-red-500/10 border border-red-500/20 text-red-400');
    el.innerHTML = msg;
}

function closeBillModal() {
    document.getElementById('billModal').classList.add('hidden');
    document.getElementById('billFeedback').classList.add('hidden');
}

document.getElementById('billModal').addEventListener('click', function(e) {
    if (e.target === this) closeBillModal();
});
</script>
<?php
$content = ob_get_clean();
