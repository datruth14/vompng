<?php
$pageTitle = 'Gamepad - vomp';
ob_start();
$gpTokens = (int) ($currentUser['gptokens'] ?? 0);
?>
<section class="py-10 space-y-16">

    <!-- Hero -->
    <div class="text-center max-w-2xl mx-auto animate__animated animate__fadeInUp">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/20 mb-6">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.744 8.05C16.242 7.008 14.268 6.5 12 6.5s-4.242.508-5.744 1.55C4.92 9.03 4 10.615 4 12.5c0 1.885.92 3.47 2.256 4.45C7.758 18.492 9.732 19 12 19s4.242-.508 5.744-1.55C19.08 16.47 20 14.885 20 13c0-1.885-.92-3.47-2.256-4.45zM8.5 10.5h2v2h-2v-2zm5 0h2v2h-2v-2z" /></svg>
        </div>
        <h1 class="text-5xl md:text-6xl font-black text-white tracking-tight mb-4">Gamepad</h1>
        <p class="text-gray-400 text-lg leading-relaxed">Play fun web games, rack up high scores, and earn <span class="text-emerald-400 font-bold">GPTokens</span> that can be converted to Vomp Coins when we launch rewards.</p>
    </div>

    <!-- GPTokens Balance -->
    <div class="max-w-xs mx-auto text-center animate__animated animate__bounceIn">
        <div class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <p class="text-xs uppercase tracking-widest font-black text-gray-500 mb-2">Your GPTokens</p>
            <p class="text-5xl font-black text-emerald-400"><?php echo number_format($gpTokens); ?></p>
            <p class="text-xs text-gray-500 mt-3">Score points in games to earn more</p>
        </div>
    </div>

    <!-- How It Works -->
    <div class="max-w-4xl mx-auto space-y-8">
        <h2 class="text-3xl font-black text-white text-center animate__animated animate__fadeInDown">How It Works</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center animate__animated animate__fadeInUp" style="animation-delay:0.05s">
                <div class="w-14 h-14 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.401.604-.401.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.959.401v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z" /></svg>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Play Games</h3>
                <p class="text-gray-400 text-sm">Choose from a variety of fun web games and start playing instantly. No downloads, no hassle.</p>
            </div>
            <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center animate__animated animate__fadeInUp" style="animation-delay:0.1s">
                <div class="w-14 h-14 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Earn GPTokens</h3>
                <p class="text-gray-400 text-sm">Your high score is your reward. The better you play, the more GPTokens you earn.</p>
            </div>
            <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center animate__animated animate__fadeInUp" style="animation-delay:0.15s">
                <div class="w-14 h-14 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Convert to Vomp Coins</h3>
                <p class="text-gray-400 text-sm">GPTokens will be convertible to Vomp Coins when the rewards program launches. Start stacking now!</p>
            </div>
        </div>
    </div>

    <!-- Games Grid -->
    <div class="max-w-4xl mx-auto space-y-8">
        <h2 class="text-3xl font-black text-white text-center animate__animated animate__fadeInDown">Available Games</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center animate__animated animate__fadeInUp" style="animation-delay:0.05s">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Space Shooter</h3>
                <p class="text-gray-400 text-sm mb-6">Blast through asteroids and alien ships. The further you go, the more GPTokens you earn!</p>
                <button disabled class="px-8 py-3 rounded-2xl bg-emerald-600 text-white font-black text-sm opacity-50 cursor-not-allowed">Coming Soon</button>
            </div>
            <div class="glass-morphism rounded-[2rem] p-8 border border-white/10 text-center animate__animated animate__fadeInUp" style="animation-delay:0.1s">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" /></svg>
                </div>
                <h3 class="text-xl font-black text-white mb-2">Color Swipe</h3>
                <p class="text-gray-400 text-sm mb-6">Match the colors as fast as you can. Quick reflexes earn big GPTokens!</p>
                <a href="/game/color-swipe" class="inline-block px-8 py-3 rounded-2xl bg-gradient-to-r from-pink-500 to-rose-600 text-white font-black text-sm hover:scale-105 transition-transform">Play Now</a>
            </div>
        </div>
    </div>

    <!-- FAQ / Info -->
    <div class="max-w-2xl mx-auto glass-morphism rounded-[2.5rem] p-8 md:p-10 border border-white/10 animate__animated animate__fadeInUp">
        <h2 class="text-2xl font-black text-white mb-6">Frequently Asked Questions</h2>
        <div class="space-y-6">
            <div>
                <p class="text-white font-bold mb-1">What are GPTokens?</p>
                <p class="text-gray-400 text-sm">GPTokens (Gamepad Tokens) are rewards earned by playing games on Gamepad. Your high score in each game determines how many GPTokens you earn.</p>
            </div>
            <div>
                <p class="text-white font-bold mb-1">Can I convert GPTokens to Vomp Coins?</p>
                <p class="text-gray-400 text-sm">Not yet. GPToken-to-Vomp Coin conversion will be available when the rewards program officially launches. Keep playing and stacking in the meantime!</p>
            </div>
            <div>
                <p class="text-white font-bold mb-1">How do I earn more GPTokens?</p>
                <p class="text-gray-400 text-sm">Play games and aim for high scores. Better performance = more GPTokens. Check back often as new games are added regularly.</p>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
?>