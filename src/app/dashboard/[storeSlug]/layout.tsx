import { createClient } from '@/utils/supabase/server';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import DashboardSidebar from '@/components/dashboard/DashboardSidebar';

export default async function DashboardLayout({
  children,
  params,
}: {
  children: React.ReactNode;
  params: Promise<{ storeSlug: string }>;
}) {
  const cookieStore = await cookies()
  const supabase = createClient(cookieStore)

  const { data: { user }, error } = await supabase.auth.getUser()
  if (error || !user) {
    redirect('/login');
  }

  const { storeSlug } = await params;

  const { data: store } = await supabase
    .from('stores')
    .select('slug, name, token_balance')
    .eq('slug', storeSlug)
    .eq('owner_id', user.id)
    .single();

  if (!store) {
    redirect('/login');
  }

  return (
    <div className="flex min-h-screen bg-gray-950">
      <DashboardSidebar
        storeSlug={store.slug}
        storeName={store.name}
        tokenBalance={store.token_balance}
      />
      <main className="flex-1 overflow-auto">
        {children}
      </main>
    </div>
  );
}
