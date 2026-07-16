<?php
$pageTitle = 'Forgot Password - vomp';
ob_start();
?>
<section class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-xl glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 bg-[#ff610a]/10 blur-[80px] rounded-full"></div>
        <div class="relative z-10">
            <h1 class="text-4xl font-black text-white mb-2 tracking-tight">Forgot password?</h1>
            <p class="text-gray-400 font-medium mb-10">Enter your email and we'll send you an OTP to reset it.</p>

            <?php if (isset($error) && $error): ?>
                <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-medium"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (isset($success) && $success): ?>
                <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="/api/forgot_password.php" class="space-y-8">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                </div>

                <button type="submit" class="btn-press w-full py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">
                    Send OTP
                </button>
            </form>

            <p class="text-center text-sm font-medium text-gray-500 mt-10">
                <a href="/login" class="text-[#ff610a] hover:text-[#ff8c3a] font-bold">&larr; Back to login</a>
            </p>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
?>
