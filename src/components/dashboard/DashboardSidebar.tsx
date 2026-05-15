'use client';

import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { createClient } from '@/utils/supabase/client';
import {
  Zap,
  LayoutDashboard,
  ShoppingBag,
  Settings,
  Coins,
  LogOut,
  ExternalLink,
  ChevronRight,
} from 'lucide-react';

interface DashboardSidebarProps {
  storeSlug: string;
  storeName: string;
  tokenBalance: number;
}

export default function DashboardSidebar({
  storeSlug,
  storeName,
  tokenBalance,
}: DashboardSidebarProps) {
  const pathname = usePathname();
  const router = useRouter();
  const supabase = createClient();

  const navItems = [
    { href: `/dashboard/${storeSlug}`, label: 'Overview', icon: LayoutDashboard },
    { href: `/dashboard/${storeSlug}/products`, label: 'Products', icon: ShoppingBag },
    { href: `/dashboard/${storeSlug}/tokens`, label: 'Tokens', icon: Coins },
    { href: `/dashboard/${storeSlug}/settings`, label: 'Settings', icon: Settings },
  ];

  const isActive = (href: string) =>
    pathname === href || (href !== `/dashboard/${storeSlug}` && pathname.startsWith(href));

  const handleSignOut = async () => {
    await supabase.auth.signOut();
    router.push('/');
  };

  return (
    <aside className="w-64 min-h-screen bg-gray-900/80 border-r border-white/10 flex flex-col">
      {/* Logo */}
      <div className="p-6 border-b border-white/10">
        <Link href="/" className="flex items-center gap-2 mb-5">
          <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
            <Zap className="w-4 h-4 text-white" />
          </div>
          <span className="text-lg font-bold gradient-text">VOMP</span>
        </Link>
        <div className="glass rounded-xl p-3">
          <p className="text-xs text-gray-500 mb-0.5">Store</p>
          <p className="font-semibold text-sm truncate">{storeName}</p>
          <Link
            href={`/store/${storeSlug}`}
            target="_blank"
            className="flex items-center gap-1 text-xs text-indigo-400 hover:text-indigo-300 mt-1.5 transition-colors"
          >
            View store <ExternalLink className="w-3 h-3" />
          </Link>
        </div>
      </div>

      {/* Nav */}
      <nav className="flex-1 p-4 space-y-1">
        {navItems.map((item) => (
          <Link
            key={item.href}
            href={item.href}
            className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all group ${
              isActive(item.href)
                ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30'
                : 'text-gray-400 hover:text-white hover:bg-white/5'
            }`}
          >
            <item.icon className="w-4 h-4 flex-shrink-0" />
            {item.label}
            {isActive(item.href) && (
              <ChevronRight className="w-3.5 h-3.5 ml-auto" />
            )}
          </Link>
        ))}
      </nav>

      {/* Token meter */}
      <div className="p-4 border-t border-white/10">
        <div className="glass rounded-xl p-4 mb-4">
          <div className="flex items-center justify-between mb-2">
            <span className="text-xs text-gray-400">Token Balance</span>
            <Link
              href={`/dashboard/${storeSlug}/tokens`}
              className="text-xs text-indigo-400 hover:text-indigo-300 transition-colors"
            >
              Top up
            </Link>
          </div>
          <div className="flex items-baseline gap-1 mb-2">
            <span className="text-2xl font-black text-indigo-400">{tokenBalance}</span>
            <span className="text-xs text-gray-500">tokens left</span>
          </div>
          <div className="w-full h-1.5 bg-gray-700 rounded-full overflow-hidden">
            <div
              className="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all"
              style={{ width: `${Math.min(100, (tokenBalance / 50) * 100)}%` }}
            />
          </div>
        </div>

        <button
          id="btn-signout"
          onClick={handleSignOut}
          className="btn-press w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-500 hover:text-red-400 hover:bg-red-500/10 transition-all"
        >
          <LogOut className="w-4 h-4" />
          Sign out
        </button>
      </div>
    </aside>
  );
}
