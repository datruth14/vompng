<?php
$pageTitle = 'GPToken Exchange - vomp';
$gpTokens = (int) ($currentUser['gptokens'] ?? 0);
$threshold = 1000000;
$exchangeRate = 50;
$canExchange = $gpTokens >= $threshold;
$progressPct = min(100, round(($gpTokens / $threshold) * 100, 1));
ob_start();
?>
<section class="py-10 space-y-12">
    <div class="text-center max-w-2xl mx-auto animate__animated animate__fadeInUp">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/20 mb-6">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">GPToken Exchange</h1>
        <p class="text-gray-400 text-lg">Convert your hard-earned GPTokens to Vomp Coins.</p>
    </div>

    <div class="max-w-md mx-auto space-y-8">
        <!-- Balance Card -->
        <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center animate__animated animate__bounceIn">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-2">Your GPTokens</p>
            <p class="text-5xl font-black text-emerald-400"><?php echo number_format($gpTokens); ?></p>
            <p class="text-xs text-gray-500 mt-3">Score points in games to earn more</p>
        </div>

        <!-- Exchange Rate Card -->
        <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 animate__animated animate__fadeInUp">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-4 text-center">Exchange Rate</p>
            <div class="flex items-center justify-center gap-4 text-2xl font-black">
                <span class="text-emerald-400">1,000,000 GPT</span>
                <span class="text-gray-600 text-xl">→</span>
                <span class="text-[#ff610a]">50 VC</span>
            </div>
            <p class="text-gray-500 text-xs text-center mt-4">1 Vomp Coin (VC) = 20,000 GPTokens</p>
        </div>

        <!-- Progress Card -->
        <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 animate__animated animate__fadeInUp" style="animation-delay:0.1s">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-4 text-center">Progress to Exchange</p>

            <?php if ($canExchange): ?>
                <div class="text-center mb-6">
                    <div class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Threshold Reached!
                    </div>
                </div>
                <button id="exchangeBtn" class="btn-press w-full py-5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black text-lg shadow-xl shadow-emerald-500/20 hover:scale-[1.02] transition-all">
                    Exchange 1,000,000 GPTokens for 50 VC
                </button>
                <div id="exchangeMsg" class="mt-4"></div>
            <?php else: ?>
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-400 font-medium"><?php echo number_format($gpTokens); ?> / <?php echo number_format($threshold); ?></span>
                        <span class="text-gray-500"><?php echo $progressPct; ?>%</span>
                    </div>
                    <div class="w-full h-4 rounded-full bg-white/5 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 transition-all" style="width:<?php echo $progressPct; ?>%"></div>
                    </div>
                </div>
                <p class="text-gray-500 text-xs text-center">Need <?php echo number_format($threshold - $gpTokens); ?> more GPTokens to reach the exchange threshold. Keep playing!</p>
            <?php endif; ?>
        </div>

        <div class="text-center">
            <a href="/game" class="text-gray-500 hover:text-white text-sm font-medium transition-colors">← Back to Games</a>
        </div>
    </div>
</section>

<?php if ($canExchange): ?>
<script>
document.getElementById('exchangeBtn')?.addEventListener('click', async function() {
    var btn = this;
    btn.disabled = true;
    btn.textContent = 'Processing...';
    document.getElementById('exchangeMsg').innerHTML = '';

    try {
        var res = await fetch('/api/game_exchange.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        });
        var data = await res.json();
        if (data.success) {
            document.getElementById('exchangeMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-bold">Success! Exchanged 1,000,000 GPTokens for 50 Vomp Coins.</div>';
            btn.textContent = 'Exchange Complete';
            setTimeout(function() { location.reload(); }, 2000);
        } else {
            document.getElementById('exchangeMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">' + (data.error || 'Exchange failed') + '</div>';
            btn.disabled = false;
            btn.textContent = 'Exchange 1,000,000 GPTokens for 50 VC';
        }
    } catch(e) {
        document.getElementById('exchangeMsg').innerHTML = '<div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold">Network error. Please try again.</div>';
        btn.disabled = false;
        btn.textContent = 'Exchange 1,000,000 GPTokens for 50 VC';
    }
});
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
?>
