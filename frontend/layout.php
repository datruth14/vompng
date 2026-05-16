<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'VomP App'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/theme.css">
</head>
<body class="bg-gray-950 text-gray-100 selection:bg-indigo-500/30">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] animate-blob"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] animate-blob" style="animation-delay:2s"></div>
    </div>

    <nav class="relative z-10 max-w-7xl mx-auto px-4 pt-8">
        <div class="glass-morphism rounded-3xl px-6 py-4 flex justify-between items-center border border-white/10">
            <a href="/" class="inline-flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-white font-black">V</span>
                </div>
                <span class="text-3xl font-black text-white tracking-tight">VOMP</span>
            </a>
            <div class="flex items-center gap-3">
                <?php if ($currentUser): ?>
                    <span class="text-gray-300 text-sm font-bold uppercase tracking-wider"><?php echo htmlspecialchars($currentUser['name']); ?></span>
                    <a href="/logout" class="btn-press px-5 py-2.5 rounded-xl bg-rose-500/20 border border-rose-400/30 text-rose-200 font-black text-sm hover:bg-rose-500/30 transition-all">Logout</a>
                <?php else: ?>
                    <a href="/login" class="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white font-black text-sm hover:bg-white/10 transition-all">Login</a>
                    <a href="/register" class="btn-press px-5 py-2.5 rounded-xl bg-indigo-500 text-white font-black text-sm shadow-lg shadow-indigo-500/20 hover:bg-indigo-400 transition-all">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="relative z-10 max-w-7xl mx-auto p-4 md:p-8">
        <?php if (isset($error)): ?>
            <div class="px-4 py-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-bold mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="px-4 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm font-bold mb-6">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php echo $content ?? ''; ?>
    </main>
</body>
</html>
