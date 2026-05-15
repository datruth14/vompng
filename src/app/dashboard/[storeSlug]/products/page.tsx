'use client';

import { useState, useEffect, useRef } from 'react';
import { useParams } from 'next/navigation';
import {
  Plus,
  Pencil,
  Trash2,
  Loader2,
  X,
  Upload,
  Image as ImageIcon,
  Video,
  Package,
  Check,
  AlertCircle,
} from 'lucide-react';

interface Product {
  _id: string;
  name: string;
  description: string;
  price: number;
  mediaUrl: string;
  mediaType: 'image' | 'video';
  isAvailable: boolean;
}

const emptyForm = {
  name: '',
  description: '',
  price: '',
  mediaUrl: '',
  mediaType: 'image' as 'image' | 'video',
  isAvailable: true,
};

export default function ProductsPage() {
  const params = useParams();
  const storeSlug = params.storeSlug as string;
  const fileInputRef = useRef<HTMLInputElement>(null);

  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const [modalOpen, setModalOpen] = useState(false);
  const [editing, setEditing] = useState<Product | null>(null);
  const [form, setForm] = useState({ ...emptyForm });
  const [saving, setSaving] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    setLoading(true);
    const res = await fetch(`/api/products?storeSlug=${storeSlug}`);
    const data = await res.json();
    setProducts(data.products ?? []);
    setLoading(false);
  };

  const openModal = (product?: Product) => {
    if (product) {
      setEditing(product);
      setForm({
        name: product.name,
        description: product.description,
        price: String(product.price),
        mediaUrl: product.mediaUrl,
        mediaType: product.mediaType,
        isAvailable: product.isAvailable,
      });
    } else {
      setEditing(null);
      setForm({ ...emptyForm });
    }
    setError('');
    setModalOpen(true);
  };

  const closeModal = () => {
    setModalOpen(false);
    setEditing(null);
    setError('');
  };

  const handleUpload = async (file: File) => {
    setUploading(true);
    const fd = new FormData();
    fd.append('file', file);
    fd.append('folder', `vomp/${storeSlug}/products`);

    try {
      const res = await fetch('/api/upload', { method: 'POST', body: fd });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error);
      setForm((prev) => ({
        ...prev,
        mediaUrl: data.url,
        mediaType: file.type.startsWith('video') ? 'video' : 'image',
      }));
    } catch (e: unknown) {
      setError('Upload failed. Please try again.');
    } finally {
      setUploading(false);
    }
  };

  const handleSave = async () => {
    if (!form.name.trim()) { setError('Product name is required'); return; }
    if (!form.price || isNaN(Number(form.price))) { setError('Valid price is required'); return; }

    setSaving(true);
    setError('');

    try {
      if (editing) {
        const res = await fetch('/api/products', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ productId: editing._id, ...form, price: Number(form.price) }),
        });
        if (!res.ok) throw new Error();
      } else {
        const res = await fetch('/api/products', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ storeSlug, ...form, price: Number(form.price) }),
        });
        if (!res.ok) throw new Error();
      }

      await fetchProducts();
      closeModal();
      setSuccess(editing ? 'Product updated!' : 'Product added!');
      setTimeout(() => setSuccess(''), 3000);
    } catch {
      setError('Failed to save product');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (productId: string) => {
    if (!confirm('Delete this product?')) return;
    await fetch(`/api/products?productId=${productId}`, { method: 'DELETE' });
    await fetchProducts();
  };

  return (
    <div className="p-8">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 className="text-2xl font-black mb-1">Products</h1>
          <p className="text-gray-400 text-sm">{products.length} products in your store</p>
        </div>
        <button
          id="btn-add-product"
          onClick={() => openModal()}
          className="btn-press flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold text-sm hover:opacity-90 transition-all"
        >
          <Plus className="w-4 h-4" />
          Add Product
        </button>
      </div>

      {success && (
        <div className="mb-5 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-sm flex items-center gap-2">
          <Check className="w-4 h-4" />
          {success}
        </div>
      )}

      {loading ? (
        <div className="flex items-center justify-center py-24">
          <Loader2 className="w-8 h-8 text-indigo-400 animate-spin" />
        </div>
      ) : products.length === 0 ? (
        <div className="text-center py-24 glass rounded-2xl">
          <Package className="w-12 h-12 text-gray-600 mx-auto mb-4" />
          <p className="text-gray-400 font-medium mb-2">No products yet</p>
          <p className="text-gray-600 text-sm mb-6">Add your first product to start selling</p>
          <button
            onClick={() => openModal()}
            className="btn-press px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold text-sm"
          >
            Add Your First Product
          </button>
        </div>
      ) : (
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
          {products.map((product) => (
            <div key={product._id} className="glass rounded-2xl overflow-hidden group hover:border-white/20 transition-all hover:-translate-y-0.5">
              {/* Media */}
              <div className="aspect-square bg-gray-800 relative overflow-hidden">
                {product.mediaUrl ? (
                  product.mediaType === 'video' ? (
                    <video
                      src={product.mediaUrl}
                      className="w-full h-full object-cover"
                      muted
                      loop
                      onMouseEnter={(e) => (e.target as HTMLVideoElement).play()}
                      onMouseLeave={(e) => { (e.target as HTMLVideoElement).pause(); (e.target as HTMLVideoElement).currentTime = 0; }}
                    />
                  ) : (
                    <img
                      src={product.mediaUrl}
                      alt={product.name}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                  )
                ) : (
                  <div className="w-full h-full flex items-center justify-center">
                    <Package className="w-12 h-12 text-gray-700" />
                  </div>
                )}
                {!product.isAvailable && (
                  <div className="absolute inset-0 bg-gray-900/60 flex items-center justify-center">
                    <span className="px-3 py-1 rounded-full bg-gray-700 text-xs text-gray-300">Unavailable</span>
                  </div>
                )}
                {product.mediaType === 'video' && (
                  <div className="absolute top-2 right-2 px-2 py-1 rounded-lg bg-black/60 text-xs text-white flex items-center gap-1">
                    <Video className="w-3 h-3" /> Video
                  </div>
                )}
              </div>

              <div className="p-4">
                <h3 className="font-semibold text-sm mb-1 truncate">{product.name}</h3>
                <p className="text-indigo-400 font-bold mb-3">₦{product.price.toLocaleString()}</p>
                <div className="flex gap-2">
                  <button
                    onClick={() => openModal(product)}
                    className="btn-press flex-1 flex items-center justify-center gap-1.5 py-2 rounded-lg glass text-xs text-gray-400 hover:text-white transition-all"
                  >
                    <Pencil className="w-3.5 h-3.5" />
                    Edit
                  </button>
                  <button
                    onClick={() => handleDelete(product._id)}
                    className="btn-press p-2 rounded-lg glass text-gray-600 hover:text-red-400 hover:bg-red-500/10 transition-all"
                  >
                    <Trash2 className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Modal */}
      {modalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
          <div className="w-full max-w-md glass rounded-3xl p-7 shadow-2xl shadow-black/50">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-bold">{editing ? 'Edit Product' : 'Add Product'}</h2>
              <button onClick={closeModal} className="p-1.5 rounded-lg hover:bg-white/10 text-gray-400 hover:text-white transition-all">
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Media upload */}
            <div
              className="mb-5 relative border-2 border-dashed border-gray-700 rounded-xl overflow-hidden cursor-pointer hover:border-indigo-500/50 transition-colors"
              style={{ aspectRatio: form.mediaUrl ? 'auto' : '16/9', minHeight: '140px' }}
              onClick={() => fileInputRef.current?.click()}
            >
              {form.mediaUrl ? (
                form.mediaType === 'video' ? (
                  <video src={form.mediaUrl} className="w-full h-40 object-cover" muted />
                ) : (
                  <img src={form.mediaUrl} alt="preview" className="w-full h-40 object-cover" />
                )
              ) : (
                <div className="h-full min-h-[140px] flex flex-col items-center justify-center gap-2">
                  {uploading ? (
                    <Loader2 className="w-8 h-8 text-indigo-400 animate-spin" />
                  ) : (
                    <>
                      <Upload className="w-8 h-8 text-gray-600" />
                      <p className="text-xs text-gray-500">Click to upload image or video</p>
                    </>
                  )}
                </div>
              )}
              {form.mediaUrl && (
                <button
                  onClick={(e) => { e.stopPropagation(); setForm((p) => ({ ...p, mediaUrl: '' })); }}
                  className="absolute top-2 right-2 p-1 rounded-lg bg-black/60 text-white hover:bg-red-500/80 transition-colors"
                >
                  <X className="w-3.5 h-3.5" />
                </button>
              )}
            </div>
            <input
              ref={fileInputRef}
              type="file"
              accept="image/*,video/*"
              className="hidden"
              onChange={(e) => e.target.files?.[0] && handleUpload(e.target.files[0])}
            />

            <div className="space-y-4">
              <div>
                <label className="block text-xs font-medium text-gray-400 mb-1.5">Product Name *</label>
                <input
                  id="product-name"
                  type="text"
                  value={form.name}
                  onChange={(e) => setForm((p) => ({ ...p, name: e.target.value }))}
                  placeholder="e.g. Blue Ankara Dress"
                  className="w-full bg-gray-800/60 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors"
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-400 mb-1.5">Price (₦) *</label>
                <input
                  id="product-price"
                  type="number"
                  value={form.price}
                  onChange={(e) => setForm((p) => ({ ...p, price: e.target.value }))}
                  placeholder="e.g. 15000"
                  className="w-full bg-gray-800/60 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors"
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-400 mb-1.5">Description</label>
                <textarea
                  id="product-desc"
                  value={form.description}
                  onChange={(e) => setForm((p) => ({ ...p, description: e.target.value }))}
                  placeholder="Describe this product..."
                  rows={2}
                  className="w-full bg-gray-800/60 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors resize-none"
                />
              </div>

              <label className="flex items-center gap-3 cursor-pointer">
                <div
                  className={`w-9 h-5 rounded-full transition-colors ${form.isAvailable ? 'bg-indigo-500' : 'bg-gray-700'}`}
                  onClick={() => setForm((p) => ({ ...p, isAvailable: !p.isAvailable }))}
                >
                  <div className={`w-4 h-4 bg-white rounded-full mt-0.5 transition-transform ${form.isAvailable ? 'translate-x-4' : 'translate-x-0.5'}`} />
                </div>
                <span className="text-sm text-gray-300">Available for purchase</span>
              </label>
            </div>

            {error && (
              <div className="mt-4 flex items-center gap-2 text-red-400 text-sm">
                <AlertCircle className="w-4 h-4" />
                {error}
              </div>
            )}

            <div className="flex gap-3 mt-6">
              <button onClick={closeModal} className="btn-press flex-1 py-2.5 rounded-xl glass text-gray-400 text-sm font-medium hover:text-white transition-all">
                Cancel
              </button>
              <button
                id="btn-save-product"
                onClick={handleSave}
                disabled={saving}
                className="btn-press flex-[2] py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold flex items-center justify-center gap-2 disabled:opacity-60 hover:opacity-90 transition-all"
              >
                {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : null}
                {saving ? 'Saving...' : editing ? 'Update Product' : 'Add Product'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
