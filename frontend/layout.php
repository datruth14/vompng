<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $pageTitle ?? 'vomp App'; ?></title>
    <link rel="icon" type="image/png" href="/assets/img/logo.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/img/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#030712">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/theme.css">
</head>
<body class="bg-gray-950 text-gray-100 selection:bg-[#ff610a]/30">
    <script>
        window.APP_CURRENT_USER = <?php echo json_encode($currentUser ?? null); ?>;
        window.APP_IS_LOGGED_IN = <?php echo json_encode(!empty($currentUser)); ?>;
        window.deferredPrompt = null;
        window.addEventListener('beforeinstallprompt', (e) => { e.preventDefault(); window.deferredPrompt = e; });
        window.triggerInstall = function() {
          if (!window.deferredPrompt) {
            alert('To install VomP, use the install icon in your browser address bar, or open the browser menu and select "Install VomP".');
            return;
          }
          window.deferredPrompt.prompt();
          window.deferredPrompt.userChoice.then(() => { window.deferredPrompt = null; });
        };
    </script>
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] animate-blob"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] animate-blob" style="animation-delay:2s"></div>
    </div>

    <nav class="relative z-10 max-w-7xl mx-auto px-4 pt-8">
        <div class="glass-morphism rounded-3xl px-6 py-4 border border-white/10">
            <!-- Desktop & Mobile Header Row -->
            <div class="flex justify-between items-center">
                <a href="/" class="inline-flex items-center group">
                    <img src="/assets/img/logo.png" alt="vomp" class="h-10 w-auto group-hover:scale-105 transition-transform duration-300">
                </a>

                <!-- Hamburger Button (Mobile Only) -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg bg-white/5 border border-white/10 text-white hover:bg-white/10 transition-all">
                    <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Desktop Menu (Hidden on Mobile) -->
                <div class="hidden md:flex items-center gap-6">
                    <?php if ($currentUser): ?>
                        <a href="/dashboard" class="inline-flex items-center gap-2 py-2 px-3 text-[#ff8c3a] hover:text-[#ffb07a] font-bold text-sm transition-all duration-200 relative group">
                            <svg class="w-4 h-4 text-[#ff610a] group-hover:text-[#ffcc66] transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            <span>Dashboard</span>
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-[#ff610a] scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-200"></span>
                        </a>
                        <a href="/download" class="inline-flex items-center gap-2 py-2 px-3 text-gray-300 hover:text-white font-bold text-sm transition-all duration-200 relative group">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            <span>Download App</span>
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-[#ff610a] scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-200"></span>
                        </a>
                        <span class="inline-flex items-center gap-2 text-gray-400 text-xs font-bold uppercase tracking-wider border-l border-white/10 pl-4 py-1">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span><?php echo htmlspecialchars($currentUser['name']); ?></span>
                        </span>
                        <a href="/logout" class="inline-flex items-center gap-2 py-2 px-3 text-rose-300 hover:text-rose-100 font-bold text-sm transition-all duration-200 relative group">
                            <svg class="w-4 h-4 text-rose-400 group-hover:text-rose-200 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Logout</span>
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-rose-500 scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-200"></span>
                        </a>
                    <?php else: ?>
                        <a href="/login" class="inline-flex items-center gap-2 py-2 px-3 text-gray-300 hover:text-white font-bold text-sm transition-all duration-200 relative group">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Login</span>
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-[#ff610a] scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-200"></span>
                        </a>
                        <a href="/register" class="inline-flex items-center gap-2 py-2 px-3 text-[#ff8c3a] hover:text-[#ffb07a] font-bold text-sm transition-all duration-200 relative group">
                            <svg class="w-4 h-4 text-[#ff610a] group-hover:text-[#ffcc66] transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span>Register</span>
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-[#ff610a] scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-200"></span>
                        </a>
                        <a href="/download" class="inline-flex items-center gap-2 py-2 px-3 text-gray-300 hover:text-white font-bold text-sm transition-all duration-200 relative group">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            <span>Download App</span>
                            <span class="absolute bottom-0 left-3 right-3 h-[2px] bg-[#ff610a] scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-200"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mobile Menu (Hidden by default) -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pt-4 border-t border-white/10 space-y-3 flex flex-col items-center">
                <?php if ($currentUser): ?>
                    <a href="/dashboard" class="flex items-center gap-2 py-2.5 px-5 text-[#ff8c3a] hover:text-[#ffb07a] font-bold text-sm transition-all duration-200">
                        <svg class="w-4 h-4 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <div class="flex items-center gap-2 py-1 text-gray-500 text-xs font-black uppercase tracking-wider">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span><?php echo htmlspecialchars($currentUser['name']); ?></span>
                    </div>
                    <a href="/download" class="flex items-center gap-2 py-2.5 px-5 text-gray-300 hover:text-white font-bold text-sm transition-all duration-200">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        <span>Download App</span>
                    </a>
                    <a href="/logout" class="flex items-center gap-2 py-2.5 px-5 text-rose-300 hover:text-rose-100 font-bold text-sm transition-all duration-200">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </a>
                <?php else: ?>
                    <a href="/login" class="flex items-center gap-2 py-2.5 px-5 text-gray-300 hover:text-white font-bold text-sm transition-all duration-200">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Login</span>
                    </a>
                    <a href="/register" class="flex items-center gap-2 py-2.5 px-5 text-[#ff8c3a] hover:text-[#ffb07a] font-bold text-sm transition-all duration-200">
                        <svg class="w-4 h-4 text-[#ff610a]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Register</span>
                    </a>
                    <a href="/download" class="flex items-center gap-2 py-2.5 px-5 text-gray-300 hover:text-white font-bold text-sm transition-all duration-200">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        <span>Download App</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
        /* Mobile menu toggle functionality */
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });

        /* Close mobile menu when a link is clicked */
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            });
        });
    </script>

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

    <?php if (in_array($requestPath, ['', 'marketplace', 'products', 'stores', 'download']) || str_starts_with($requestPath, 'store/')): ?>
    <!-- Share FAB -->
    <button id="shareBtn" class="fixed bottom-24 left-4 z-50 w-12 h-12 rounded-full bg-black text-[#ff610a] border-2 border-[#ff610a] shadow-lg shadow-[#ff610a]/20 hover:bg-[#1a1a1a] transition-all flex items-center justify-center" onclick="shareCurrentPage()">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" /></svg>
    </button>
    <?php endif; ?>

    <!-- Nav Drawer Toggle (stays on left edge when collapsed) -->
    <button id="navToggleBtn" class="fixed bottom-6 left-4 z-50 w-12 h-12 rounded-full bg-[#ff610a] text-white shadow-lg shadow-[#ff610a]/30 hover:bg-[#e05500] transition-all flex items-center justify-center">
        <svg id="navToggleIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
    </button>

    <!-- Overlay backdrop -->
    <div id="navOverlay" class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

    <!-- Nav Drawer (slides from left) -->
    <div id="navDrawer" class="fixed top-0 left-0 z-40 h-full w-72 bg-gray-950/95 border-r border-white/10 shadow-2xl transition-transform duration-300 -translate-x-full will-change-transform overflow-y-auto">
        <div class="flex flex-col items-stretch gap-2 p-6 pt-20">
            <a href="/" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                <span>Home</span>
            </a>
            <a href="/marketplace" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
                <span>Marketplace</span>
            </a>
            <?php if ($currentUser): ?>
                <a href="/games" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span>Game Pad</span>
                </a>
                <a href="/profile" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    <span>Profile</span>
                </a>
                <a href="/tokens" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Vomp Coins</span>
                </a>
                <div class="border-t border-white/10 my-2"></div>
                <a href="/download" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Download App</span>
                </a>
                <?php if ($currentUser && ($currentUser['role'] ?? 'user') === 'admin'): ?>
                    <a href="/admin" class="flex items-center gap-3 py-3 px-4 rounded-xl text-[#ff610a] hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        <span>Admin</span>
                    </a>
                <?php endif; ?>
                <a href="/logout" class="flex items-center gap-3 py-3 px-4 rounded-xl text-rose-300 hover:text-rose-100 hover:bg-white/5 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>Logout</span>
                </a>
            <?php else: ?>
                <div class="border-t border-white/10 my-2"></div>
                <a href="/login" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>Login</span>
                </a>
                <a href="/register" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    <span>Register</span>
                </a>
                <a href="/download" class="flex items-center gap-3 py-3 px-4 rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all font-bold text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    <span>Download App</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        /* Nav drawer toggle */
        const navToggleBtn = document.getElementById('navToggleBtn');
        const navDrawer = document.getElementById('navDrawer');
        const navOverlay = document.getElementById('navOverlay');
        const navToggleIcon = document.getElementById('navToggleIcon');
        const shareBtn = document.getElementById('shareBtn');
        let navOpen = false;

        function openNav() {
            navOpen = true;
            navDrawer.classList.remove('-translate-x-full');
            navOverlay.classList.remove('opacity-0', 'pointer-events-none');
            navToggleBtn.style.bottom = '1rem';
            if (shareBtn) shareBtn.style.bottom = '5.5rem';
            navToggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
        }

        function closeNav() {
            navOpen = false;
            navDrawer.classList.add('-translate-x-full');
            navOverlay.classList.add('opacity-0', 'pointer-events-none');
            navToggleBtn.style.bottom = '';
            if (shareBtn) shareBtn.style.bottom = '';
            navToggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />';
        }

        navToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (navOpen) closeNav(); else openNav();
        });

        navOverlay.addEventListener('click', closeNav);

        /* Native share */
        function shareCurrentPage() {
            const url = window.location.href;
            const title = document.title || 'vomp';
            if (navigator.share) {
                navigator.share({ title: title, url: url }).catch(() => {});
            } else {
                const existing = document.getElementById('shareModal');
                if (existing) existing.remove();
                const overlay = document.createElement('div');
                overlay.className = 'fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm';
                overlay.id = 'shareModal';
                overlay.addEventListener('click', () => overlay.remove());
                const box = document.createElement('div');
                box.className = 'bg-gray-900 rounded-2xl p-6 border border-white/10 max-w-sm w-full mx-4 shadow-2xl';
                box.addEventListener('click', (e) => e.stopPropagation());
                box.innerHTML = '<p class="text-white font-black text-lg mb-4">Share this page</p>' +
                    '<div class="flex items-center gap-2 bg-white/5 rounded-xl px-4 py-3 border border-white/10">' +
                    '<span class="text-gray-300 text-sm truncate flex-1">' + url + '</span>' +
                    '<button class="shrink-0 px-3 py-1.5 rounded-lg bg-[#ff610a] text-white text-xs font-bold" id="copyBtn">Copy</button>' +
                    '</div>' +
                    '<button class="mt-4 text-sm text-gray-500 hover:text-white font-bold" id="closeShareBtn">Close</button>';
                box.querySelector('#copyBtn').addEventListener('click', function() {
                    navigator.clipboard.writeText(url).then(() => { this.textContent = 'Copied!'; });
                });
                box.querySelector('#closeShareBtn').addEventListener('click', () => overlay.remove());
                overlay.appendChild(box);
                document.body.appendChild(overlay);
            }
        }

        /* Close drawer when a link is clicked */
        navDrawer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeNav);
        });

        /* Auto-fit: shrink text font-size to fit its container without overflow */
        function autoFitText() {
            document.querySelectorAll('.text-fit').forEach(el => {
                const parent = el.parentElement;
                if (!parent) return;
                const ps = getComputedStyle(parent);
                const padX = parseFloat(ps.paddingLeft) + parseFloat(ps.paddingRight);
                const maxWidth = parent.clientWidth - (isNaN(padX) ? 0 : padX);
                if (maxWidth < 10) return;
                el.style.whiteSpace = 'nowrap';
                el.style.maxWidth = '100%';
                let fontSize = parseFloat(getComputedStyle(el).fontSize);
                if (!fontSize || fontSize < 1) fontSize = 36;
                el.style.fontSize = fontSize + 'px';
                let limit = 0;
                while (el.scrollWidth > maxWidth && fontSize > 8 && limit < 100) {
                    fontSize -= 0.5;
                    el.style.fontSize = fontSize + 'px';
                    limit++;
                }
            });
        }
        autoFitText();
        window.addEventListener('load', autoFitText);
        window.addEventListener('resize', autoFitText);
        if (window.ResizeObserver) {
            new ResizeObserver(autoFitText).observe(document.body);
        }
        setTimeout(autoFitText, 100);
        setTimeout(autoFitText, 500);
    </script>

    <script>
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js').catch(function(err) {
        console.warn('SW registration failed:', err);
      });
    }
    </script>

</body>
</html>
