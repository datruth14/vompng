import { NextRequest, NextResponse } from 'next/server';
import { createClient } from '@/utils/supabase/server';
import { cookies } from 'next/headers';
import { initializePaystackTransaction, PLANS, PlanKey } from '@/lib/paystack';

export async function POST(req: NextRequest) {
  try {
    const cookieStore = await cookies()
    const supabase = createClient(cookieStore)

    const { data: { user }, error: authError } = await supabase.auth.getUser()
    if (authError || !user) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const { storeSlug, plan } = await req.json();

    if (!PLANS[plan as PlanKey]) {
      return NextResponse.json({ error: 'Invalid plan' }, { status: 400 });
    }

    const { data: store, error: storeError } = await supabase
      .from('stores')
      .select('id, slug')
      .eq('slug', storeSlug)
      .eq('owner_id', user.id)
      .single();

    if (storeError || !store) {
      return NextResponse.json({ error: 'Store not found' }, { status: 404 });
    }

    const selectedPlan = PLANS[plan as PlanKey];
    const appUrl = process.env.NEXT_PUBLIC_APP_URL ?? 'http://localhost:3000';

    const transaction = await initializePaystackTransaction({
      email: user.email!,
      amount: selectedPlan.amount,
      metadata: {
        storeId: store.id, // uuid
        storeSlug: store.slug,
        plan,
        tokens: selectedPlan.tokens,
        userId: user.id,
      },
      callbackUrl: `${appUrl}/dashboard/${storeSlug}/tokens?payment=success`,
    });

    return NextResponse.json({ authorizationUrl: transaction.authorization_url });
  } catch (error) {
    console.error(error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}
