import { createClient } from '@/utils/supabase/server';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';

export default async function DashboardRootPage() {
  const cookieStore = await cookies()
  const supabase = createClient(cookieStore)

  const { data: { user }, error } = await supabase.auth.getUser()
  if (error || !user) {
    redirect('/login');
  }

  const { data: store } = await supabase
    .from('stores')
    .select('slug')
    .eq('owner_id', user.id)
    .order('created_at', { ascending: true })
    .limit(1)
    .single();

  if (!store) {
    redirect('/onboarding');
  }

  redirect(`/dashboard/${store.slug}`);
}
