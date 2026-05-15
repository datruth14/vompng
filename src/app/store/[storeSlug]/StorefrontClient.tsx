'use client';

import { useState } from 'react';
import {
  ShoppingCart,
  MessageCircle,
  X,
  Play,
  Phone,
  Mail,
  Zap,
  AlertTriangle,
  Loader2,
  Package,
  Star,
  ChevronRight,
  ExternalLink,
} from 'lucide-react';

interface StoreData {
  _id: string;
  slug: string;
  name: string;
  description: string;
  logoUrl: string;
  contactPhone: string;
  contactEmail: string;
  heroImageUrl: string;
  heroColor: string;
  accentColor: string;
  token_balance: number;
}

interface ProductData {
  _id: string;
  name: string;
  description: string;
  price: number;
  mediaUrl: string;
  mediaType: 'image' | 'video';
}

interface CartItem extends ProductData {
  qty: number;
}

interface StorefrontClientProps {
  store: StoreData;
  products: ProductData[];
}

export default function StorefrontClient({ store, products }: StorefrontClientProps) {
  const [cart, setCart] = useState<CartItem[]>([]);
  const [cartOpen, setCartOpen] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState<ProductData | null>(null);
  const [ordering, setOrdering] = useState(false);
  const [orderLimitReached, setOrderLimitReached] = useState(false);

  const heroGradient = `linear-gradient(135deg, ${store.heroColor ?? '#6366f1'}, ${store.accentColor ?? '#8b5cf6'})`;

  const addToCart = (product: ProductData) => {
    setCart((prev) => {
      const exists = prev.find((i) => i._id === product._id);
      if (exists) return prev.map((i) => i._id === product._id ? { ...i, qty: i.qty + 1 } : i);
      return [...prev, { ...product, qty: 1 }];
    });
    setCartOpen(true);
  };

  const removeFromCart = (id: string) =>
    setCart((prev) => prev.filter((i) => i._id !== id));

  const updateQty = (id: string, qty: number) => {
    if (qty <= 0) removeFromCart(id);
    else setCart((prev) => prev.map((i) => i._id === id ? { ...i, qty } : i));
  };

  const totalItems = cart.reduce((sum, i) => sum + i.qty, 0);
  const totalPrice = cart.reduce((sum, i) => sum + i.price * i.qty, 0);

  const handleWhatsAppOrder = async () => {
    if (cart.length === 0) return;
    setOrdering(true);

    try {
      const res = await fetch('/api/tokens/deduct', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ storeSlug: store.slug }),
      });

      if (res.status === 402) {
        setOrderLimitReached(true);
        setOrdering(false);
        return;
      }

      const data = await res.json();

      if (data.whatsappUrl) {
        // Build a richer message with cart items
        const itemsList = cart
          .map((i) => `• ${i.name} x${i.qty} — ₦${(i.price * i.qty).toLocaleString()}`)
          .join('\n');
        const message = encodeURIComponent(
          `Hi! I'd like to order from ${store.name}:\n\n${itemsList}\n\nTotal: ₦${totalPrice.toLocaleString()}\n\nPlease confirm availability and payment details. Thank you!`
        );
        const whatsappNumber = store.contactPhone.replace(/\D/g, '');
        window.open(`https://wa.me/${whatsappNumber}?text=${message}`, '_blank');
        setCart([]);
        setCartOpen(false);
      }
    } catch {
      alert('Something went wrong. Please try again.');
    } finally {
      setOrdering(false);
    }
  };

  const hasTokens = store.token_balance > 0;

  return (
    <div className="min-h-screen bg-gray-50" style={{ fontFamily: "'Inter', sans-serif" }}>
      {/* Hero */}
      <div
        className="relative text-white overflow-hidden"
        style={{
          background: store.heroImageUrl
            ? `url(${store.heroImageUrl}) center/cover no-repeat`
            : heroGradient,
          minHeight: '280px',
        }}
      >
        {store.heroImageUrl && (
          <div className="absolute inset-0" style={{ background: `linear-gradient(135deg, ${store.heroColor}cc, ${store.accentColor}99)` }} />
        )}
        <div className="relative z-10 px-4 py-8 max-w-5xl mx-auto">
          {/* Header */}
          <div className="flex items-center justify-between mb-8">
            <div className="flex items-center gap-3">
              {store.logoUrl ? (
                <img
                  src={store.logoUrl}
                  alt={store.name}
                  className="w-12 h-12 rounded-full object-cover border-2 border-white/30"
                />
              ) : (
                <div
                  className="w-12 h-12 rounded-full border-2 border-white/30 flex items-center justify-center text-white font-black text-lg"
                  style={{ background: 'rgba(255,255,255,0.2)' }}
                >
                  {store.name[0]}
                </div>
              )}
              <div>
                <h1 className="font-black text-xl leading-tight">{store.name}</h1>
                {store.contactPhone && (
                  <p className="text-white/70 text-xs flex items-center gap-1">
                    <Phone className="w-3 h-3" />
                    {store.contactPhone}
                  </p>
                )}
              </div>
            </div>

            {/* Cart button */}
            <button
              id="btn-open-cart"
              onClick={() => setCartOpen(true)}
              className="relative p-3 rounded-full bg-white/20 backdrop-blur-sm hover:bg-white/30 transition-colors"
            >
              <ShoppingCart className="w-5 h-5" />
              {totalItems > 0 && (
                <span className="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center">
                  {totalItems}
                </span>
              )}
            </button>
          </div>

          {/* Store description */}
          {store.description && (
            <p className="text-white/80 text-sm max-w-lg leading-relaxed">{store.description}</p>
          )}
        </div>
      </div>

      {/* Order limit notice */}
      {orderLimitReached && (
        <div className="max-w-5xl mx-auto px-4 py-3 my-3">
          <div className="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
            <AlertTriangle className="w-5 h-5 flex-shrink-0 text-amber-500" />
            <div>
              <p className="font-semibold text-sm">Order Limit Reached</p>
              <p className="text-xs mt-0.5">This store has reached its order limit. Please contact the store owner directly.</p>
            </div>
            {store.contactPhone && (
              <a
                href={`https://wa.me/${store.contactPhone.replace(/\D/g, '')}`}
                target="_blank"
                className="ml-auto flex-shrink-0 px-3 py-1.5 rounded-lg bg-amber-200 text-amber-800 text-xs font-semibold"
              >
                Contact Store
              </a>
            )}
          </div>
        </div>
      )}

      {/* Products */}
      <div className="max-w-5xl mx-auto px-4 py-8">
        <h2 className="font-black text-xl text-gray-900 mb-5">
          {products.length > 0 ? `${products.length} Products` : 'Products'}
        </h2>

        {products.length === 0 ? (
          <div className="text-center py-16">
            <Package className="w-12 h-12 text-gray-300 mx-auto mb-3" />
            <p className="text-gray-400">No products yet.</p>
          </div>
        ) : (
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            {products.map((product) => (
              <div
                key={product._id}
                className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all hover:-translate-y-0.5 cursor-pointer group"
                onClick={() => setSelectedProduct(product)}
              >
                {/* Media */}
                <div className="aspect-square bg-gray-100 relative overflow-hidden">
                  {product.mediaUrl ? (
                    product.mediaType === 'video' ? (
                      <div className="relative w-full h-full">
                        <video
                          src={product.mediaUrl}
                          className="w-full h-full object-cover"
                          muted
                          loop
                          playsInline
                          onMouseEnter={(e) => (e.target as HTMLVideoElement).play()}
                          onMouseLeave={(e) => {
                            const v = e.target as HTMLVideoElement;
                            v.pause();
                            v.currentTime = 0;
                          }}
                        />
                        <div className="absolute inset-0 flex items-center justify-center">
                          <div className="w-10 h-10 rounded-full bg-black/40 flex items-center justify-center">
                            <Play className="w-4 h-4 text-white ml-0.5" />
                          </div>
                        </div>
                      </div>
                    ) : (
                      <img
                        src={product.mediaUrl}
                        alt={product.name}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                      />
                    )
                  ) : (
                    <div className="w-full h-full flex items-center justify-center">
                      <Package className="w-10 h-10 text-gray-300" />
                    </div>
                  )}
                </div>

                <div className="p-3">
                  <h3 className="font-semibold text-gray-900 text-sm leading-tight mb-1 line-clamp-2">
                    {product.name}
                  </h3>
                  <p className="font-black text-sm" style={{ color: store.heroColor }}>
                    ₦{product.price.toLocaleString()}
                  </p>
                  <button
                    onClick={(e) => { e.stopPropagation(); addToCart(product); }}
                    disabled={!hasTokens}
                    className="mt-2 w-full py-1.5 rounded-lg text-white text-xs font-semibold transition-all btn-press disabled:opacity-40"
                    style={{ background: hasTokens ? heroGradient : undefined, backgroundColor: !hasTokens ? '#9ca3af' : undefined }}
                  >
                    Add to Cart
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Footer */}
      <footer className="border-t border-gray-200 py-6 px-4 text-center text-xs text-gray-400">
        <p className="mb-1">{store.contactEmail && `✉️ ${store.contactEmail}`}</p>
        <p>Powered by <a href="/" className="text-indigo-500 font-semibold">VOMP</a></p>
      </footer>

      {/* Product Modal */}
      {selectedProduct && (
        <div
          className="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm p-4"
          onClick={() => setSelectedProduct(null)}
        >
          <div
            className="bg-white rounded-3xl overflow-hidden w-full max-w-md shadow-2xl"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Media */}
            <div className="aspect-video bg-gray-100 relative">
              {selectedProduct.mediaUrl ? (
                selectedProduct.mediaType === 'video' ? (
                  <video src={selectedProduct.mediaUrl} className="w-full h-full object-cover" controls autoPlay muted />
                ) : (
                  <img src={selectedProduct.mediaUrl} alt={selectedProduct.name} className="w-full h-full object-cover" />
                )
              ) : (
                <div className="w-full h-full flex items-center justify-center">
                  <Package className="w-16 h-16 text-gray-300" />
                </div>
              )}
              <button
                onClick={() => setSelectedProduct(null)}
                className="absolute top-3 right-3 p-1.5 rounded-full bg-black/40 text-white hover:bg-black/60"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-5">
              <h3 className="font-black text-lg text-gray-900 mb-1">{selectedProduct.name}</h3>
              <p className="text-2xl font-black mb-2" style={{ color: store.heroColor }}>
                ₦{selectedProduct.price.toLocaleString()}
              </p>
              {selectedProduct.description && (
                <p className="text-gray-500 text-sm mb-4 leading-relaxed">{selectedProduct.description}</p>
              )}
              <button
                onClick={() => { addToCart(selectedProduct); setSelectedProduct(null); }}
                disabled={!hasTokens}
                className="w-full py-3 rounded-xl text-white font-bold flex items-center justify-center gap-2 btn-press disabled:opacity-40"
                style={{ background: hasTokens ? heroGradient : '#9ca3af' }}
              >
                <ShoppingCart className="w-5 h-5" />
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Cart Drawer */}
      {cartOpen && (
        <div className="fixed inset-0 z-50 flex justify-end">
          <div className="absolute inset-0 bg-black/40" onClick={() => setCartOpen(false)} />
          <div className="relative bg-white w-full max-w-sm h-full flex flex-col shadow-2xl">
            <div className="p-5 border-b flex items-center justify-between">
              <h2 className="font-black text-lg">Your Cart ({totalItems})</h2>
              <button onClick={() => setCartOpen(false)} className="p-1.5 rounded-lg hover:bg-gray-100">
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="flex-1 overflow-y-auto p-5 space-y-4">
              {cart.length === 0 ? (
                <div className="text-center py-12">
                  <ShoppingCart className="w-10 h-10 text-gray-300 mx-auto mb-3" />
                  <p className="text-gray-400 text-sm">Your cart is empty</p>
                </div>
              ) : (
                cart.map((item) => (
                  <div key={item._id} className="flex gap-3">
                    <div className="w-16 h-16 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                      {item.mediaUrl ? (
                        <img src={item.mediaUrl} alt={item.name} className="w-full h-full object-cover" />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center">
                          <Package className="w-5 h-5 text-gray-400" />
                        </div>
                      )}
                    </div>
                    <div className="flex-1">
                      <p className="font-semibold text-sm text-gray-900 line-clamp-1">{item.name}</p>
                      <p className="text-sm font-bold" style={{ color: store.heroColor }}>
                        ₦{(item.price * item.qty).toLocaleString()}
                      </p>
                      <div className="flex items-center gap-2 mt-1.5">
                        <button onClick={() => updateQty(item._id, item.qty - 1)} className="w-6 h-6 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center text-sm font-bold hover:bg-gray-200">-</button>
                        <span className="text-sm font-medium w-6 text-center">{item.qty}</span>
                        <button onClick={() => updateQty(item._id, item.qty + 1)} className="w-6 h-6 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center text-sm font-bold hover:bg-gray-200">+</button>
                        <button onClick={() => removeFromCart(item._id)} className="ml-auto text-gray-300 hover:text-red-400">
                          <X className="w-4 h-4" />
                        </button>
                      </div>
                    </div>
                  </div>
                ))
              )}
            </div>

            {cart.length > 0 && (
              <div className="p-5 border-t bg-gray-50">
                <div className="flex items-center justify-between mb-4">
                  <span className="text-gray-600 font-medium">Total</span>
                  <span className="text-xl font-black text-gray-900">₦{totalPrice.toLocaleString()}</span>
                </div>

                {!hasTokens ? (
                  <div className="px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm text-center">
                    <AlertTriangle className="w-4 h-4 inline mr-1" />
                    This store cannot accept orders right now.
                  </div>
                ) : (
                  <button
                    id="btn-submit-order"
                    onClick={handleWhatsAppOrder}
                    disabled={ordering}
                    className="btn-press w-full py-3.5 rounded-xl text-white font-bold flex items-center justify-center gap-2 disabled:opacity-60 transition-all"
                    style={{ background: heroGradient }}
                  >
                    {ordering ? (
                      <Loader2 className="w-5 h-5 animate-spin" />
                    ) : (
                      <MessageCircle className="w-5 h-5" />
                    )}
                    {ordering ? 'Processing...' : 'Order via WhatsApp'}
                  </button>
                )}
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
