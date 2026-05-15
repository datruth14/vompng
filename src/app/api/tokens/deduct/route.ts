import { NextRequest, NextResponse } from 'next/server';
import { createClient } from '@/utils/supabase/server';
import { cookies } from 'next/headers';

export async function POST(req: NextRequest) {
  try {
    const cookieStore = await cookies()
    const supabase = createClient(cookieStore)

    const { storeSlug } = await req.json();

    if (!storeSlug) {
      return NextResponse.json({ error: 'Store slug required' }, { status: 400 });
    }

    const { data: store, error: storeError } = await supabase
      .from('stores')
      .select('id, token_balance, contact_phone, name')
      .eq('slug', storeSlug)
      .eq('is_active', true)
      .single();

    if (storeError || !store) {
      return NextResponse.json({ error: 'Store not found' }, { status: 404 });
    }

    if (store.token_balance <= 0) {
      return NextResponse.json(
        { error: 'Order limit reached', code: 'NO_TOKENS' },
        { status: 402 }
      );
    }

    // Decrement token balance
    const { data: updated, error: updateError } = await supabase
      .from('stores')
      .update({ token_balance: store.token_balance - 1 })
      .eq('id', store.id)
      .eq('token_balance', store.token_balance) // optimistic lock
      .select('token_balance')
      .single();

    if (updateError || !updated) {
      return NextResponse.json(
        { error: 'Order limit reached', code: 'NO_TOKENS' },
        { status: 402 }
      );
    }

    // Log transaction
    await supabase
      .from('token_transactions')
      .insert({
        store_id: store.id,
        type: 'debit',
        amount: 1,
        description: 'WhatsApp order redirect',
      });

    // Build WhatsApp URL
    const whatsappNumber = store.contact_phone.replace(/\D/g, '');
    const message = encodeURIComponent(
      `Hi! I'm interested in placing an order from ${store.name}.`
    );
    const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${message}`;

    return NextResponse.json({
      success: true,
      whatsappUrl,
      remainingTokens: updated.token_balance,
    });
  } catch (error) {
    console.error(error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}
