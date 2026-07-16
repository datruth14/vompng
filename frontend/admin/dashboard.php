<?php
$pageTitle = 'Admin Dashboard - vomp';
ob_start();
?>
<section class="py-6 md:py-10 space-y-12">
    <header>
        <p class="text-xs uppercase tracking-[0.2em] font-black text-[#ff610a] mb-2">Super Admin</p>
        <h1 class="text-5xl font-black text-white tracking-tight">Admin Dashboard</h1>
    </header>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Total Users</p>
            <p class="text-4xl md:text-5xl font-black text-white break-all"><?php echo number_format($totalUsers); ?></p>
        </div>
        <div class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Total Stores</p>
            <p class="text-4xl md:text-5xl font-black text-white break-all"><?php echo number_format($totalStores); ?></p>
        </div>
        <div class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Total Products</p>
            <p class="text-4xl md:text-5xl font-black text-white break-all"><?php echo number_format($totalProducts); ?></p>
        </div>
        <div class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-white/10">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Token Transactions</p>
            <p class="text-4xl md:text-5xl font-black text-[#ff610a] break-all"><?php echo number_format($totalTransactions); ?></p>
        </div>
        <div class="glass-morphism rounded-[2rem] p-6 md:p-8 border border-emerald-500/20" style="background: rgba(5,150,105,0.08);">
            <p class="text-xs uppercase tracking-wider font-black text-gray-500 mb-3">Commission Earned</p>
            <p class="text-4xl md:text-5xl font-black text-emerald-400 break-all">₦<?php echo number_format($commissionSummary['total_commission']); ?></p>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <a href="/admin/users" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
            <h3 class="text-white font-black text-xl mb-2">Manage Users</h3>
            <p class="text-gray-400 text-sm">View all registered users, their stores, and account status.</p>
        </a>
        <a href="/admin/stores" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
            <h3 class="text-white font-black text-xl mb-2">Manage Stores</h3>
            <p class="text-gray-400 text-sm">View all stores, toggle active status, and monitor activity.</p>
        </a>
        <a href="/admin/products" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
            <h3 class="text-white font-black text-xl mb-2">Manage Products</h3>
            <p class="text-gray-400 text-sm">Browse all products across every store.</p>
        </a>
        <a href="/admin/orders" class="glass-morphism rounded-2xl p-6 border border-white/10 hover:bg-white/10 transition-all">
            <h3 class="text-white font-black text-xl mb-2">Order & Token Log</h3>
            <p class="text-gray-400 text-sm">View all token transactions and order activity.</p>
        </a>
    </div>
</section>
<?php
$content = ob_get_clean();
?>
