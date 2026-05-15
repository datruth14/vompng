import { notFound } from 'next/navigation';
import { createClient } from '@supabase/supabase-js';
import StorefrontClient from './StorefrontClient';
import type { Metadata } from 'next';

const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL!;
const supabaseKey = process.env.NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY!;

const supabase = createClient(supabaseUrl, supabaseKey);

interface StorefrontPageProps {
  params: Promise<{ storeSlug: string }>;
}

export async function generateMetadata({ params }: StorefrontPageProps): Promise<Metadata> {
  const { storeSlug } = await params;

  const { data: store } = await supabase
    .from('stores')
    .select('*')
    .eq('slug', storeSlug)
    .eq('is_active', true)
    .single();

  if (!store) {
    return { title: 'Store Not Found' };
  }

  return {
    title: `${store.name} — Shop Now`,
    description: store.description || `Browse products at ${store.name}`,
    openGraph: {
      title: store.name,
      description: store.description || `Browse products at ${store.name}`,
      images: store.hero_image_url ? [store.hero_image_url] : [],
    },
  };
}

export default async function StorefrontPage({ params }: StorefrontPageProps) {
  const { storeSlug } = await params;

  const { data: store } = await supabase
    .from('stores')
    .select('*')
    .eq('slug', storeSlug)
    .eq('is_active', true)
    .single();

  if (!store) notFound();

  const { data: products } = await supabase
    .from('products')
    .select('*')
    .eq('store_id', store.id)
    .eq('is_available', true)
    .order('created_at', { ascending: false });

  // Serialize for client
  const storeData = JSON.parse(JSON.stringify(store));
  const productsData = JSON.parse(JSON.stringify(products || []));

  return <StorefrontClient store={storeData} products={productsData} />;
}
