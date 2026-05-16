<?php
$pageTitle = 'Register - VomP';
ob_start();
?>
<section class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-2xl glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-500/10 blur-[80px] rounded-full"></div>
        <div class="relative z-10">
            <h2 class="text-4xl font-black text-white tracking-tight mb-2">Join the future.</h2>
            <p class="text-gray-400 font-medium mb-10">Create your account and launch your store in one flow.</p>

            <form method="POST" action="/api/register" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Full Name</label>
                    <input type="text" name="name" required placeholder="Amara Okafor" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Store Name</label>
                    <input type="text" name="storeName" required placeholder="Lagos Gadget Hub" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Store Description</label>
                    <textarea name="storeDescription" rows="3" placeholder="What do you sell?" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Contact Phone</label>
                    <input type="tel" name="contactPhone" required placeholder="+234..." class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all">
                </div>

                <button type="submit" class="btn-press w-full py-5 rounded-2xl bg-indigo-500 text-white font-black text-lg shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all">
                    Create Account
                </button>

                <p class="text-center text-sm font-medium text-gray-500 pt-2">
                    Already have an account? <a href="/login" class="text-indigo-400 hover:text-indigo-300 font-bold">Login here</a>
                </p>
            </form>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
?>
