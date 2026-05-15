import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css';

const inter = Inter({ subsets: ['latin'], variable: '--font-inter' });

export const metadata: Metadata = {
  title: 'VOMP — Launch Your Store in Minutes',
  description:
    'Create a beautiful online storefront instantly. Sell products, accept WhatsApp orders, and grow your business with VOMP.',
  keywords: 'online store, WhatsApp commerce, storefront, Nigeria, ecommerce',
  openGraph: {
    title: 'VOMP — Launch Your Store in Minutes',
    description: 'Create a beautiful online storefront instantly.',
    type: 'website',
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en" className={inter.variable} suppressHydrationWarning>
      <body className="antialiased bg-gray-950 text-gray-100" suppressHydrationWarning>{children}</body>
    </html>
  );
}
