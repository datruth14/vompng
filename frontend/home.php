<?php
$pageTitle = 'Home - VomP';
ob_start();
?>
<section class="py-12 md:py-20">
    <div class="max-w-4xl mx-auto text-center mb-14">
        <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white mb-4">Sell Smarter With <span class="text-gradient">VOMP</span></h1>
        <p class="text-gray-400 text-lg md:text-xl font-medium">The same premium dashboard experience you had before, now running on a lightweight PHP backend.</p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="/register" class="btn-press px-8 py-4 rounded-2xl bg-indigo-500 text-white font-black text-lg shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all">Start Selling</a>
            <a href="/login" class="px-8 py-4 rounded-2xl glass-morphism border border-white/10 text-white font-black text-lg hover:bg-white/10 transition-all">Sign In</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <h3 class="text-xl font-black text-white mb-2">Beautiful Storefront</h3>
            <p class="text-gray-400">Keep your products organized and presentable with a clean merchant flow.</p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <h3 class="text-xl font-black text-white mb-2">Fast Signup Flow</h3>
            <p class="text-gray-400">Create account, create store, and start managing inventory in a few steps.</p>
        </article>
        <article class="glass-morphism rounded-[2rem] p-8 border border-white/10">
            <h3 class="text-xl font-black text-white mb-2">Simple Local Data</h3>
            <p class="text-gray-400">SQLite keeps your environment easy to run and maintain during development.</p>
        </article>
    </div>
</section>
<?php
$content = ob_get_clean();
?>
