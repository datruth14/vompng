<?php
/*
 * Home page template for unauthenticated visitors.
 */

$pageTitle = 'Home - vomp';
ob_start();
?>

<!-- Hero Section -->
<section class="py-12 md:py-20">
    <div class="max-w-4xl mx-auto text-center mb-14">
        <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white mb-4">Sell Smarter With <span class="text-gradient">vomp</span></h1>
        <p class="text-gray-400 text-lg md:text-xl font-medium">Create your online store in minutes, list products with zero upfront cost, and receive orders directly via WhatsApp. No technical skills required.</p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <?php if (!empty($currentUser)): ?>
                <a href="/dashboard" class="btn-press px-8 py-4 rounded-2xl bg-emerald-500 text-white font-black text-lg shadow-xl shadow-emerald-500/20 hover:bg-emerald-400 transition-all">Go to Dashboard</a>
            <?php else: ?>
                <a href="/register" class="btn-press px-8 py-4 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">Start Selling — It's Free</a>
                <?php if ($currentUser): ?>
                    <a href="/dashboard" class="px-8 py-4 rounded-2xl bg-[#ff610a] text-white font-black text-lg hover:bg-[#e05500] transition-all shadow-xl shadow-[#ff610a]/20">Dashboard</a>
                <?php else: ?>
                    <a href="/login" class="px-8 py-4 rounded-2xl glass-morphism border border-white/10 text-white font-black text-lg hover:bg-white/10 transition-all">Sign In</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats -->
    <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
        <div class="glass-morphism rounded-2xl p-6 border border-white/10 text-center">
            <p class="text-3xl md:text-4xl font-black text-white mb-1">50</p>
            <p class="text-xs uppercase tracking-widest font-black text-gray-500">Free Vomp Coins</p>
        </div>
        <div class="glass-morphism rounded-2xl p-6 border border-white/10 text-center">
            <p class="text-3xl md:text-4xl font-black text-white mb-1">10</p>
            <p class="text-xs uppercase tracking-widest font-black text-gray-500">Vomp Coins Per Product</p>
        </div>
        <div class="glass-morphism rounded-2xl p-6 border border-white/10 text-center">
            <p class="text-3xl md:text-4xl font-black text-white mb-1">100%</p>
            <p class="text-xs uppercase tracking-widest font-black text-gray-500">WhatsApp Orders</p>
        </div>
        <div class="glass-morphism rounded-2xl p-6 border border-white/10 text-center">
            <p class="text-3xl md:text-4xl font-black text-white mb-1">₦20</p>
            <p class="text-xs uppercase tracking-widest font-black text-gray-500">Per Vomp Coin</p>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-16 md:py-24">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-3">Getting Started</p>
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">How It Works</h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">From zero to selling in under 5 minutes. No coding, no complex setup.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass-morphism rounded-[2rem] p-8 md:p-10 border border-white/10 text-center relative">
                <div class="w-14 h-14 rounded-2xl bg-[#ff610a]/10 border border-[#ff610a]/20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-black text-[#ff610a]">1</span>
                </div>
                <h3 class="text-xl font-black text-white mb-3">Create Your Account</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Sign up with your name, email, and store details. Your storefront is generated automatically with a custom URL.</p>
            </div>
            <div class="glass-morphism rounded-[2rem] p-8 md:p-10 border border-white/10 text-center relative">
                <div class="w-14 h-14 rounded-2xl bg-[#ff610a]/10 border border-[#ff610a]/20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-black text-[#ff610a]">2</span>
                </div>
                <h3 class="text-xl font-black text-white mb-3">List Your Products</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Add product photos, set prices, and write descriptions. Each listing uses 10 Vomp Coins from your balance.</p>
            </div>
            <div class="glass-morphism rounded-[2rem] p-8 md:p-10 border border-white/10 text-center relative">
                <div class="w-14 h-14 rounded-2xl bg-[#ff610a]/10 border border-[#ff610a]/20 flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-black text-[#ff610a]">3</span>
                </div>
                <h3 class="text-xl font-black text-white mb-3">Receive Orders</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Buyers click "Order via WhatsApp" and messages come directly to your phone. No app to install, no middleman.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-16 md:py-24">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-3">Why Choose vomp</p>
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">Everything You Need to Sell</h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">Built for Nigerian entrepreneurs who want a simple, effective way to sell online.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 hover:bg-white/[0.02] transition-all">
                <div class="w-12 h-12 rounded-xl bg-[#ff610a]/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
                </div>
                <h3 class="text-lg font-black text-white mb-2">Beautiful Storefront</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Each store gets a clean, mobile-friendly page with custom branding — colors, logo, hero image, and your own URL.</p>
            </article>

            <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 hover:bg-white/[0.02] transition-all">
                <div class="w-12 h-12 rounded-xl bg-[#ff610a]/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                </div>
                <h3 class="text-lg font-black text-white mb-2">WhatsApp Ordering</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Buyers place orders directly through WhatsApp. Messages land straight on your phone — no missed notifications, no extra apps.</p>
            </article>

            <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 hover:bg-white/[0.02] transition-all">
                <div class="w-12 h-12 rounded-xl bg-[#ff610a]/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-lg font-black text-white mb-2">Vomp Coin-Powered Listings</h3>
                <p class="text-gray-400 text-sm leading-relaxed">New stores get 50 free Vomp Coins. Each product listing costs just 10 Vomp Coins. Top up anytime — 50 Vomp Coins minimum at ₦20 each.</p>
            </article>

            <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 hover:bg-white/[0.02] transition-all">
                <div class="w-12 h-12 rounded-xl bg-[#ff610a]/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                </div>
                <h3 class="text-lg font-black text-white mb-2">Zero Upfront Cost</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Registration is completely free. You only pay for Vomp Coin top-ups when you're ready to list more products beyond your free balance.</p>
            </article>

            <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 hover:bg-white/[0.02] transition-all">
                <div class="w-12 h-12 rounded-xl bg-[#ff610a]/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                </div>
                <h3 class="text-lg font-black text-white mb-2">Full Dashboard Control</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Manage products, track Vomp Coin balance, view transaction history, and update store settings — all from one clean dashboard.</p>
            </article>

            <article class="glass-morphism rounded-[2rem] p-8 border border-white/10 hover:bg-white/[0.02] transition-all">
                <div class="w-12 h-12 rounded-xl bg-[#ff610a]/10 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                </div>
                <h3 class="text-lg font-black text-white mb-2">Fast & Lightweight</h3>
                <p class="text-gray-400 text-sm leading-relaxed">Built with a lean PHP backend and SQLite — no heavy frameworks, no complex servers. Fast load times even on basic hosting.</p>
            </article>
        </div>
    </div>
</section>

<!-- WhatsApp Spotlight -->
<section class="py-16 md:py-24">
    <div class="max-w-5xl mx-auto glass-morphism rounded-[2.5rem] p-8 md:p-14 border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-72 h-72 bg-[#25D366]/5 blur-[100px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-[#ff610a]/5 blur-[100px] rounded-full"></div>
        <div class="relative z-10 flex flex-col lg:flex-row items-center gap-10">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#25D366]/10 border border-[#25D366]/20 text-[#25D366] text-xs font-black uppercase tracking-widest mb-6">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp Integration
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-4">Orders Come Directly to Your Phone</h2>
                <p class="text-gray-400 text-base leading-relaxed max-w-xl">
                    When a buyer clicks "Order via WhatsApp," a pre-filled message with product details is sent straight to your WhatsApp. 
                    No email, no form submissions, no missed opportunities — just a direct conversation with your customer.
                </p>
                <ul class="mt-6 space-y-3">
                    <li class="flex items-center gap-3 text-sm text-gray-300">
                        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        No additional apps or accounts needed
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-300">
                        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        Product details auto-included in message
                    </li>
                    <li class="flex items-center gap-3 text-sm text-gray-300">
                        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        100% free for both buyers and sellers
                    </li>
                </ul>
            </div>
            <div class="flex-shrink-0">
                <div class="w-56 h-56 md:w-72 md:h-72 rounded-[2rem] bg-gradient-to-br from-[#25D366] to-[#128C7E] flex items-center justify-center shadow-2xl shadow-[#25D366]/20">
                    <svg class="w-24 h-24 md:w-32 md:h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 md:py-24">
    <div class="max-w-4xl mx-auto text-center glass-morphism rounded-[2.5rem] p-10 md:p-16 border border-white/10">
        <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">Ready to Start Selling?</h2>
        <p class="text-gray-400 text-lg max-w-xl mx-auto mb-10">
            Join Nigeria's growing marketplace. Create your store in under 2 minutes — no credit card required.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <?php if (!empty($currentUser)): ?>
                <a href="/dashboard" class="btn-press px-10 py-5 rounded-2xl bg-emerald-500 text-white font-black text-lg shadow-xl shadow-emerald-500/20 hover:bg-emerald-400 transition-all">Go to Dashboard</a>
            <?php else: ?>
                <a href="/register" class="btn-press px-10 py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">Create Your Free Store</a>
                <a href="/login" class="px-10 py-5 rounded-2xl glass-morphism border border-white/10 text-white font-black text-lg hover:bg-white/10 transition-all">I Already Have an Account</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Footer -->
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
