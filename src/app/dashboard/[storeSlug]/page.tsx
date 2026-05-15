import { createClient } from '@/utils/supabase/server';
import { cookies } from 'next/headers';
import Link from 'next/link';
import {
  ShoppingBag,
  Coins,
  TrendingUp,
  ArrowRight,
  Package,
  Zap,
  Eye,
  Activity,
  Plus,
} from 'lucide-react';

export default async function DashboardOverview({
  params,
}: {
  params: Promise<{ storeSlug: string }>;
}) {
  const cookieStore = await cookies()
  const supabase = createClient(cookieStore)

  const { data: { user }, error } = await supabase.auth.getUser()
  if (error || !user) {
    return null;
  }

  const { storeSlug } = await params;

  const { data: store } = await supabase
    .from('stores')
    .select('*')
    .eq('slug', storeSlug)
    .eq('owner_id', user.id)
    .single();

  if (!store) return null;

  const { count: productCount } = await supabase
    .from('products')
    .select('*', { count: 'exact', head: true })
    .eq('store_id', store.id);

  const { data: recentTransactions } = await supabase
    .from('token_transactions')
    .select('*')
    .eq('store_id', store.id)
    .order('created_at', { ascending: false })
    .limit(5);

  const transactions = recentTransactions || [];

  const stats = [
    {
      label: 'Token Balance',
      value: store.token_balance,
      icon: Coins,
      color: 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
      sub: 'Order clicks left',
    },
    {
      label: 'Live Products',
      value: productCount || 0,
      icon: ShoppingBag,
      color: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
      sub: 'Visible to customers',
    },
    {
      label: 'Current Plan',
      value: store.plan.charAt(0).toUpperCase() + store.plan.slice(1),
      icon: Zap,
      color: 'bg-purple-500/10 text-purple-400 border-purple-500/20',
      sub: 'Subscription level',
    },
  ];

  return (
    <div className="p-12 max-w-7xl mx-auto space-y-16">
      <div className="flex flex-col md:flex-row md:items-end justify-between gap-10">
        <div>
          <h1 className="text-5xl font-black text-white tracking-tight mb-3">Overview</h1>
          <p className="text-gray-500 font-medium tracking-wide text-lg flex items-center gap-3">
            Managing <span className="text-indigo-400">{store.name}</span>
            <span className="w-1.5 h-1.5 rounded-full bg-gray-700" />
            <Link href={`/store/${storeSlug}`} target="_blank" className="text-xs uppercase font-black hover:text-white transition-colors flex items-center gap-2">
              View Store <Eye className="w-4 h-4" />
            </Link>
          </p>
        </div>
        
        <div className="flex items-center gap-4">
          <Link
            href={`/dashboard/${storeSlug}/products`}
            className="btn-press flex items-center gap-3 px-8 py-4 rounded-2xl bg-white text-gray-950 font-black text-sm shadow-xl"
          >
            <Plus className="w-5 h-5" />
            Add Product
          </Link>
          <Link
            href={`/dashboard/${storeSlug}/tokens`}
            className="btn-press flex items-center gap-3 px-8 py-4 rounded-2xl glass-morphism border-white/10 text-white font-black text-sm hover:bg-white/5 transition-all"
          >
            <Zap className="w-5 h-5" />
            Buy Tokens
          </Link>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-10">
        {stats.map((stat) => (
          <div key={stat.label} className="glass-morphism rounded-[2.5rem] p-10 border border-white/5 group relative overflow-hidden shadow-xl">
            <div className="absolute top-0 right-0 w-32 h-32 bg-white/5 blur-3xl rounded-full translate-x-10 -translate-y-10 group-hover:scale-150 transition-transform duration-700" />
            <div className={`w-14 h-14 rounded-2xl ${stat.color} flex items-center justify-center mb-8 border shadow-lg`}>
              <stat.icon className="w-7 h-7" />
            </div>
            <div className="space-y-2">
              <div className="text-5xl font-black text-white tracking-tighter">{stat.value}</div>
              <div className="text-xs uppercase tracking-[0.2em] font-black text-gray-500">{stat.label}</div>
              <div className="text-sm font-medium text-gray-600">{stat.sub}</div>
            </div>
          </div>
        ))}
      </div>

      <div className="grid lg:grid-cols-5 gap-12">
        {/* Token warning */}
        {store.token_balance < 10 && (
          <div className="lg:col-span-5 relative overflow-hidden rounded-[3rem] p-12 bg-gradient-to-r from-amber-500 to-orange-600 shadow-2xl shadow-amber-500/20">
            <div className="absolute top-0 right-0 w-80 h-80 bg-white/20 blur-[100px] -translate-y-1/2 translate-x-1/2 rounded-full" />
            <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-10">
              <div className="flex items-center gap-8">
                <div className="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center shadow-xl">
                  <Zap className="w-10 h-10 text-white fill-current" />
                </div>
                <div>
                  <h3 className="text-3xl font-black text-white mb-2">Low Token Balance!</h3>
                  <p className="text-amber-50 text-lg font-medium opacity-95">Customers won't be able to send orders soon. Top up now.</p>
                </div>
              </div>
              <Link
                href={`/dashboard/${storeSlug}/tokens`}
                className="btn-press px-10 py-5 rounded-2xl bg-white text-orange-600 font-black text-lg shadow-2xl"
              >
                Refill Tokens
              </Link>
            </div>
          </div>
        )}

        {/* Recent Activity */}
        <div className="lg:col-span-3 glass-morphism rounded-[3rem] p-12 border border-white/5 shadow-xl">
          <div className="flex items-center justify-between mb-10">
            <div className="flex items-center gap-4">
              <div className="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center shadow-inner">
                <Activity className="w-6 h-6 text-indigo-400" />
              </div>
              <h2 className="text-2xl font-black text-white">Recent Activity</h2>
            </div>
            <Link href={`/dashboard/${storeSlug}/tokens`} className="text-xs font-black uppercase text-gray-500 hover:text-white transition-colors tracking-widest">
              Full History
            </Link>
          </div>

          {transactions.length === 0 ? (
            <div className="py-20 text-center space-y-4">
              <TrendingUp className="w-12 h-12 text-gray-800 mx-auto" />
              <p className="text-gray-600 font-medium">No recent token activity to show.</p>
            </div>
          ) : (
            <div className="space-y-6">
              {transactions.map((transaction) => (
                <div key={transaction.id.toString()} className="flex items-center justify-between p-5 rounded-2xl bg-white/5 border border-white/5 hover:border-white/10 transition-colors">
                  <div className="flex items-center gap-4">
                    <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${transaction.type === 'credit' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400'}`}>
                      {transaction.type === 'credit' ? <Plus className="w-4 h-4" /> : <Activity className="w-4 h-4" />}
                    </div>
                    <div>
                      <p className="text-sm font-bold text-white">{transaction.description}</p>
                      <p className="text-[10px] uppercase font-black text-gray-600 tracking-wider">
                        {new Date(transaction.created_at).toLocaleDateString('en-NG', {
                          day: 'numeric', month: 'short', year: 'numeric',
                        })}
                      </p>
                    </div>
                  </div>
                  <span className={`text-lg font-black ${transaction.type === 'credit' ? 'text-emerald-400' : 'text-rose-400'}`}>
                    {transaction.type === 'credit' ? '+' : '-'}{transaction.amount}
                  </span>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Quick Links */}
        <div className="lg:col-span-2 space-y-6">
          {[
            { label: 'Manage Products', icon: Package, href: `/dashboard/${storeSlug}/products`, desc: 'Add or edit store items' },
            { label: 'Store Settings', icon: TrendingUp, href: `/dashboard/${storeSlug}/settings`, desc: 'Update logo and colors' },
            { label: 'Buy More Tokens', icon: Coins, href: `/dashboard/${storeSlug}/tokens`, desc: 'Refill order capacity' },
          ].map((item) => (
            <Link
              key={item.label}
              href={item.href}
              className="group block p-8 rounded-[2rem] glass-morphism border border-white/5 hover:bg-white/5 transition-all duration-300"
            >
              <div className="flex items-center justify-between mb-4">
                <div className="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center group-hover:bg-indigo-500/10 transition-colors">
                  <item.icon className="w-6 h-6 text-gray-500 group-hover:text-indigo-400 transition-colors" />
                </div>
                <ArrowRight className="w-5 h-5 text-gray-700 group-hover:text-white group-hover:translate-x-1 transition-all" />
              </div>
              <h3 className="text-lg font-black text-white mb-1">{item.label}</h3>
              <p className="text-xs font-medium text-gray-500">{item.desc}</p>
            </Link>
          ))}
        </div>
      </div>
    </div>
  );
}
