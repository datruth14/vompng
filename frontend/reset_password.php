<?php
$pageTitle = 'Reset Password - vomp';
ob_start();
?>
<section class="min-h-[70vh] flex items-center justify-center py-10">
    <div class="w-full max-w-xl glass-morphism rounded-[2.5rem] p-8 md:p-12 border border-white/10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 bg-[#ff610a]/10 blur-[80px] rounded-full"></div>
        <div class="relative z-10">
            <h1 class="text-4xl font-black text-white mb-2 tracking-tight">Reset password</h1>
            <p class="text-gray-400 font-medium mb-10">Enter the OTP sent to your email and choose a new password.</p>

            <?php if (isset($error) && $error): ?>
                <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-medium"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (isset($success) && $success): ?>
            <?php if ($waUrl): ?>
                <a href="<?php echo htmlspecialchars($waUrl); ?>" target="_blank" class="flex items-center justify-center gap-3 w-full py-5 rounded-2xl bg-emerald-600 text-white font-black text-lg shadow-xl shadow-emerald-600/20 hover:bg-emerald-500 transition-all mb-8">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Open WhatsApp to see OTP
                </a>
            <?php endif; ?>

            <form method="POST" action="/api/reset_password.php" class="space-y-8">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">OTP Code</label>
                    <input type="text" name="otp" required maxlength="6" placeholder="000000" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all text-center text-2xl font-bold tracking-[0.3em]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">New Password</label>
                    <input type="password" name="password" required minlength="6" placeholder="••••••••" class="w-full bg-white/5 border border-white/5 rounded-2xl px-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-[#ff610a]/50 focus:bg-white/[0.08] transition-all">
                </div>

                <button type="submit" class="btn-press w-full py-5 rounded-2xl bg-[#ff610a] text-white font-black text-lg shadow-xl shadow-[#ff610a]/20 hover:bg-[#e05500] transition-all">
                    Reset Password
                </button>
            </form>

            <p class="text-center text-sm font-medium text-gray-500 mt-10">
                Didn't get an OTP? <a href="/forgot-password" class="text-[#ff610a] hover:text-[#ff8c3a] font-bold">Resend</a>
            </p>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
?>
