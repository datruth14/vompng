<?php
$pageTitle = 'Bill Payment - vomp';
ob_start();
?>
<section class="py-12">
    <div class="max-w-4xl mx-auto text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4 animate__animated animate__fadeInDown">Bill Payment</h1>
        <p class="text-gray-400 text-lg animate__animated animate__fadeInUp">Pay for airtime, data, electricity, and TV subscriptions instantly.</p>
    </div>

    <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-16">
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
        <button onclick="openBillModal('cable')" class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 text-center hover:bg-white/[0.06] transition-all group animate__animated animate__fadeInUp" style="animation-delay:0.3s">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" /></svg>
            </div>
            <p class="text-white font-black text-sm md:text-base">Cable TV</p>
            <p class="text-gray-500 text-xs mt-1">DSTV, GOtv, Showmax</p>
        </button>
    </div>

    <!-- Bill Modal -->
    <div id="billModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="glass-morphism rounded-2xl p-6 md:p-8 border border-white/10 max-w-md w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 id="billModalTitle" class="text-white font-black text-xl">Select Service</h2>
                <button onclick="closeBillModal()" class="p-2 rounded-xl bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div id="billModalBody" class="space-y-3">
                <!-- Dynamically populated -->
            </div>
            <p class="text-center text-xs text-gray-600 mt-6">Powered by VomP</p>
        </div>
    </div>
</section>

<script>
var billServices = {
    airtime: {
        title: 'Buy Airtime',
        fields: [
            { label: 'Network', type: 'select', options: ['MTN', 'Glo', 'Airtel', '9mobile'] },
            { label: 'Phone Number', type: 'tel', placeholder: '08012345678' },
            { label: 'Amount (₦)', type: 'number', placeholder: '100' }
        ]
    },
    data: {
        title: 'Buy Data Bundle',
        fields: [
            { label: 'Network', type: 'select', options: ['MTN', 'Glo', 'Airtel', '9mobile'] },
            { label: 'Phone Number', type: 'tel', placeholder: '08012345678' },
            { label: 'Data Plan', type: 'select', options: ['1GB - ₦300', '2GB - ₦550', '5GB - ₦1000', '10GB - ₦1500'] }
        ]
    },
    electricity: {
        title: 'Pay Electricity Bill',
        fields: [
            { label: 'Disco', type: 'select', options: ['IKEDC', 'AEDC', 'EKEDC', 'PHED', 'KAEDCO', 'JED'] },
            { label: 'Meter Number', type: 'text', placeholder: 'Enter meter number' },
            { label: 'Amount (₦)', type: 'number', placeholder: 'Amount' }
        ]
    },
    cable: {
        title: 'Pay Cable TV',
        fields: [
            { label: 'Provider', type: 'select', options: ['DSTV', 'GOtv', 'Showmax'] },
            { label: 'Smart Card / IUC Number', type: 'text', placeholder: 'Enter smart card number' },
            { label: 'Package', type: 'select', options: ['Basic - ₦2,500', 'Standard - ₦5,000', 'Premium - ₦10,000'] }
        ]
    }
};

function openBillModal(type) {
    var svc = billServices[type];
    if (!svc) return;
    document.getElementById('billModalTitle').textContent = svc.title;
    var body = document.getElementById('billModalBody');
    body.innerHTML = '';
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
                opt.value = o;
                opt.textContent = o;
                el.appendChild(opt);
            });
        } else {
            el = document.createElement('input');
            el.type = f.type;
            el.className = 'w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 transition-all';
            if (f.placeholder) el.placeholder = f.placeholder;
        }
        wrapper.appendChild(el);
        body.appendChild(wrapper);
    });
    var btn = document.createElement('button');
    btn.className = 'btn-press w-full py-4 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all';
    btn.textContent = 'Proceed';
    btn.onclick = function() { alert('Bill payment coming soon!'); };
    body.appendChild(btn);
    document.getElementById('billModal').classList.remove('hidden');
}

function closeBillModal() {
    document.getElementById('billModal').classList.add('hidden');
}

document.getElementById('billModal').addEventListener('click', function(e) {
    if (e.target === this) closeBillModal();
});
</script>
<?php
$content = ob_get_clean();
