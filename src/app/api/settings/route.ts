import { NextRequest, NextResponse } from 'next/server';
import { createClient } from '@/utils/supabase/server';
import { cookies } from 'next/headers';

// GET store settings (for dashboard)
export async function GET(req: NextRequest) {
  try {
    const cookieStore = await cookies()
    const supabase = createClient(cookieStore)

    const { data: { user }, error: authError } = await supabase.auth.getUser()
    if (authError || !user) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const { searchParams } = new URL(req.url);
    const storeSlug = searchParams.get('storeSlug');

    const { data: store, error } = await supabase
      .from('stores')
      .select('*')
      .eq('slug', storeSlug)
      .eq('owner_id', user.id)
      .single();

    if (error || !store) {
      return NextResponse.json({ error: 'Store not found' }, { status: 404 });
    }

    return NextResponse.json({ store });
  } catch (error) {
    console.error(error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}

// PATCH update store settings
export async function PATCH(req: NextRequest) {
  try {
    const cookieStore = await cookies()
    const supabase = createClient(cookieStore)

    const { data: { user }, error: authError } = await supabase.auth.getUser()
    if (authError || !user) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const body = await req.json();
    const {
      storeSlug,
      name,
      description,
      logoUrl,
      contactPhone,
      contactEmail,
      heroImageUrl,
      heroColor,
      accentColor,
    } = body;

    const updateData: any = {};
    if (name) updateData.name = name;
    if (description !== undefined) updateData.description = description;
    if (logoUrl !== undefined) updateData.logo_url = logoUrl;
    if (contactPhone) updateData.contact_phone = contactPhone;
    if (contactEmail !== undefined) updateData.contact_email = contactEmail;
    if (heroImageUrl !== undefined) updateData.hero_image_url = heroImageUrl;
    if (heroColor) updateData.hero_color = heroColor;
    if (accentColor) updateData.accent_color = accentColor;

    const { data: store, error } = await supabase
      .from('stores')
      .update(updateData)
      .eq('slug', storeSlug)
      .eq('owner_id', user.id)
      .select()
      .single();

    if (error || !store) {
      return NextResponse.json({ error: 'Store not found or update failed' }, { status: 404 });
    }

    return NextResponse.json({ store });
  } catch (error) {
    console.error(error);
    return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
  }
}
