import { NextRequest, NextResponse } from 'next/server';
import { createClient } from '@supabase/supabase-js';
import { verifyPaystackWebhookSignature, PLANS, PlanKey } from '@/lib/paystack';

export async function POST(req: NextRequest) {
  try {
    const rawBody = await req.text();
    const signature = req.headers.get('x-paystack-signature') ?? '';

    // Verify webhook signature
    if (!verifyPaystackWebhookSignature(rawBody, signature)) {
      return NextResponse.json({ error: 'Invalid signature' }, { status: 401 });
    }

    const event = JSON.parse(rawBody);

    if (event.event !== 'charge.success') {
      return NextResponse.json({ received: true });
    }

    const { reference, metadata, amount } = event.data;
    const { storeId, plan, tokens } = metadata;

    if (!storeId || !plan || !tokens) {
      return NextResponse.json({ error: 'Invalid metadata' }, { status: 400 });
    }

    // Create Supabase client
    const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL;
    const supabaseServiceKey = process.env.SUPABASE_SERVICE_ROLE_KEY;

    if (!supabaseUrl || !supabaseServiceKey) {
      console.error('Missing Supabase environment variables');
      return NextResponse.json(
        { error: 'Server configuration error' },
        { status: 500 }
      );
    }

    const supabase = createClient(supabaseUrl, supabaseServiceKey);

    // Idempotency: check if this ref was already processed
    const { data: existing } = await supabase
      .from('token_transactions')
      .select('id')
      .eq('paystack_ref', reference)
      .single();

    if (existing) {
      return NextResponse.json({ received: true });
    }

    // Get current token balance
    const { data: store, error: storeError } = await supabase
      .from('stores')
      .select('token_balance')
      .eq('id', storeId)
      .single();

    if (storeError || !store) {
      return NextResponse.json({ error: 'Store not found' }, { status: 404 });
    }

    // Credit tokens and update plan
    const { data: updatedStore, error: updateError } = await supabase
      .from('stores')
      .update({
        token_balance: store.token_balance + Number(tokens),
        plan: plan,
      })
      .eq('id', storeId)
      .select()
      .single();

    if (updateError || !updatedStore) {
      return NextResponse.json({ error: 'Failed to update store' }, { status: 500 });
    }

    // Log the credit transaction
    await supabase
      .from('token_transactions')
      .insert({
        store_id: storeId,
        type: 'credit',
        amount: Number(tokens),
        plan,
        paystack_ref: reference,
        description: `Purchased ${tokens} tokens (${PLANS[plan as PlanKey]?.name ?? plan})`,
      });

    console.log(`✅ Credited ${tokens} tokens to store ${storeId} for ref ${reference}`);
    return NextResponse.json({ received: true });
  } catch (error) {
    console.error('Webhook error:', error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}
