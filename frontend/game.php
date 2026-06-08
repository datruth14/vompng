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

<footer class="py-16 md:py-20 border-t border-white/5 mt-16">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">
        <div class="md:col-span-2">
            <img src="/assets/img/logo.png" alt="vomp" class="h-10 w-auto mb-4">
            <p class="text-gray-400 text-sm leading-relaxed max-w-sm">vomp is Nigeria's simplest marketplace platform. Create your store, list products, and receive orders directly via WhatsApp — no technical skills required.</p>
            <div class="flex items-center gap-3 mt-5">
                <a href="mailto:support@vomp.ng" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                </a>
                <a href="https://wa.me/2349115963439" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
                <a href="https://www.youtube.com/@vompDotNg" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                <a href="https://www.facebook.com/VompNG" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="https://www.instagram.com/vomp.ng/" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
            </div>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-wider mb-4">Quick Links</h4>
            <ul class="space-y-3">
                <li><a href="/marketplace" class="text-gray-400 hover:text-white text-sm transition-colors">Marketplace</a></li>
                <?php if ($currentUser): ?>
                    <li><a href="/dashboard" class="text-gray-400 hover:text-white text-sm transition-colors">Dashboard</a></li>
                    <li><a href="/logout" class="text-gray-400 hover:text-white text-sm transition-colors">Logout</a></li>
                <?php else: ?>
                    <li><a href="/register" class="text-gray-400 hover:text-white text-sm transition-colors">Create a Store</a></li>
                    <li><a href="/login" class="text-gray-400 hover:text-white text-sm transition-colors">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-wider mb-4">Contact</h4>
            <ul class="space-y-3 text-sm text-gray-400">
                <li>support@vomp.ng</li>
                <li>(234) 9115 963 439</li>
                <li>Mowe, Ogun State, Nigeria.</li>
            </ul>
        </div>
    </div>
    <div class="max-w-6xl mx-auto mt-10 pt-6 border-t border-white/5 text-center text-xs text-gray-600">
        &copy; <?php echo date('Y'); ?> vomp. All rights reserved.
    </div>
    <div class="max-w-6xl mx-auto mt-3 text-center text-xs text-gray-700">
        vomp is a product of 14Eter Limited RC: 1865845
    </div>
</footer>
<?php
$content = ob_get_clean();
?>