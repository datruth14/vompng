'use client';

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';
import { Loader2, Check, AlertCircle, Upload, Palette, Phone, Mail, AlignLeft, Store, Image } from 'lucide-react';

export default function SettingsPage() {
  const params = useParams();
  const storeSlug = params.storeSlug as string;

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [success, setSuccess] = useState('');
  const [error, setError] = useState('');

  const [form, setForm] = useState({
    name: '',
    description: '',
    logoUrl: '',
    contactPhone: '',
    contactEmail: '',
    heroImageUrl: '',
    heroColor: '#6366f1',
    accentColor: '#8b5cf6',
  });

  useEffect(() => {
    fetch(`/api/settings?storeSlug=${storeSlug}`)
      .then((r) => r.json())
      .then(({ store }) => {
        if (store) {
          setForm({
            name: store.name ?? '',
            description: store.description ?? '',
            logoUrl: store.logoUrl ?? '',
            contactPhone: store.contactPhone ?? '',
            contactEmail: store.contactEmail ?? '',
            heroImageUrl: store.heroImageUrl ?? '',
            heroColor: store.heroColor ?? '#6366f1',
            accentColor: store.accentColor ?? '#8b5cf6',
          });
        }
      })
      .finally(() => setLoading(false));
  }, [storeSlug]);

  const handleUpload = async (e: React.ChangeEvent<HTMLInputElement>, field: 'logoUrl' | 'heroImageUrl') => {
    const file = e.target.files?.[0];
    if (!file) return;
    setUploading(true);
    const fd = new FormData();
    fd.append('file', file);
    fd.append('folder', `vomp/${storeSlug}/branding`);
    try {
      const res = await fetch('/api/upload', { method: 'POST', body: fd });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error);
      setForm((p) => ({ ...p, [field]: data.url }));
    } catch {
      setError('Upload failed');
    } finally {
      setUploading(false);
    }
  };

  const handleSave = async () => {
    if (!form.name.trim()) { setError('Store name is required'); return; }
    if (!form.contactPhone.trim()) { setError('WhatsApp number is required'); return; }

    setSaving(true);
    setError('');
    try {
      const res = await fetch('/api/settings', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ storeSlug, ...form }),
      });
      if (!res.ok) throw new Error();
      setSuccess('Settings saved successfully!');
      setTimeout(() => setSuccess(''), 3000);
    } catch {
      setError('Failed to save settings');
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-96">
        <Loader2 className="w-8 h-8 text-indigo-400 animate-spin" />
      </div>
    );
  }

  return (
    <div className="p-8 max-w-3xl">
      <div className="mb-8">
        <h1 className="text-2xl font-black mb-1">Store Settings</h1>
        <p className="text-gray-400 text-sm">Customize your store's appearance and contact details.</p>
      </div>

      {success && (
        <div className="mb-5 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 text-sm flex items-center gap-2">
          <Check className="w-4 h-4" /> {success}
        </div>
      )}
      {error && (
        <div className="mb-5 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm flex items-center gap-2">
          <AlertCircle className="w-4 h-4" /> {error}
        </div>
      )}

      <div className="space-y-6">
        {/* Basic Info */}
        <div className="glass rounded-2xl p-6">
          <h2 className="font-bold mb-5 flex items-center gap-2">
            <Store className="w-4 h-4 text-indigo-400" />
            Basic Information
          </h2>
          <div className="space-y-4">
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Store Name *</label>
              <input
                id="settings-name"
                type="text"
                value={form.name}
                onChange={(e) => setForm((p) => ({ ...p, name: e.target.value }))}
                className="w-full bg-gray-800/60 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors"
              />
            </div>
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Description</label>
              <textarea
                id="settings-desc"
                value={form.description}
                onChange={(e) => setForm((p) => ({ ...p, description: e.target.value }))}
                rows={3}
                className="w-full bg-gray-800/60 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors resize-none"
              />
            </div>
          </div>
        </div>

        {/* Contact */}
        <div className="glass rounded-2xl p-6">
          <h2 className="font-bold mb-5 flex items-center gap-2">
            <Phone className="w-4 h-4 text-indigo-400" />
            Contact Details
          </h2>
          <div className="space-y-4">
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">WhatsApp Number *</label>
              <div className="relative">
                <Phone className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600" />
                <input
                  id="settings-phone"
                  type="tel"
                  value={form.contactPhone}
                  onChange={(e) => setForm((p) => ({ ...p, contactPhone: e.target.value }))}
                  placeholder="+234 800 000 0000"
                  className="w-full bg-gray-800/60 border border-gray-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors"
                />
              </div>
            </div>
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-1.5">Email</label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600" />
                <input
                  id="settings-email"
                  type="email"
                  value={form.contactEmail}
                  onChange={(e) => setForm((p) => ({ ...p, contactEmail: e.target.value }))}
                  placeholder="store@example.com"
                  className="w-full bg-gray-800/60 border border-gray-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500 transition-colors"
                />
              </div>
            </div>
          </div>
        </div>

        {/* Branding */}
        <div className="glass rounded-2xl p-6">
          <h2 className="font-bold mb-5 flex items-center gap-2">
            <Palette className="w-4 h-4 text-indigo-400" />
            Branding & Appearance
          </h2>
          <div className="space-y-5">
            {/* Logo */}
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-2">Store Logo</label>
              <div className="flex items-center gap-4">
                <div className="w-16 h-16 rounded-xl bg-gray-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                  {form.logoUrl ? (
                    <img src={form.logoUrl} alt="logo" className="w-full h-full object-cover" />
                  ) : (
                    <Image className="w-6 h-6 text-gray-600" />
                  )}
                </div>
                <label className="btn-press cursor-pointer flex items-center gap-2 px-4 py-2 rounded-xl glass text-xs text-gray-400 hover:text-white transition-all">
                  <Upload className="w-3.5 h-3.5" />
                  {uploading ? 'Uploading...' : 'Upload Logo'}
                  <input type="file" accept="image/*" className="hidden" onChange={(e) => handleUpload(e, 'logoUrl')} />
                </label>
              </div>
            </div>

            {/* Hero Image */}
            <div>
              <label className="block text-xs font-medium text-gray-400 mb-2">Hero Banner Image</label>
              {form.heroImageUrl ? (
                <div className="relative rounded-xl overflow-hidden mb-2">
                  <img src={form.heroImageUrl} alt="hero" className="w-full h-32 object-cover" />
                  <button
                    onClick={() => setForm((p) => ({ ...p, heroImageUrl: '' }))}
                    className="absolute top-2 right-2 px-2 py-1 rounded-lg bg-black/60 text-xs text-white hover:bg-red-500/80"
                  >
                    Remove
                  </button>
                </div>
              ) : null}
              <label className="btn-press cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-xl glass text-xs text-gray-400 hover:text-white transition-all">
                <Upload className="w-3.5 h-3.5" />
                {uploading ? 'Uploading...' : 'Upload Hero Image'}
                <input type="file" accept="image/*" className="hidden" onChange={(e) => handleUpload(e, 'heroImageUrl')} />
              </label>
            </div>

            {/* Colors */}
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-medium text-gray-400 mb-1.5">Primary Color</label>
                <div className="flex items-center gap-3">
                  <input
                    id="settings-hero-color"
                    type="color"
                    value={form.heroColor}
                    onChange={(e) => setForm((p) => ({ ...p, heroColor: e.target.value }))}
                    className="w-10 h-10 rounded-lg cursor-pointer border-0 bg-transparent"
                  />
                  <span className="text-sm text-gray-400 font-mono">{form.heroColor}</span>
                </div>
              </div>
              <div>
                <label className="block text-xs font-medium text-gray-400 mb-1.5">Accent Color</label>
                <div className="flex items-center gap-3">
                  <input
                    id="settings-accent-color"
                    type="color"
                    value={form.accentColor}
                    onChange={(e) => setForm((p) => ({ ...p, accentColor: e.target.value }))}
                    className="w-10 h-10 rounded-lg cursor-pointer border-0 bg-transparent"
                  />
                  <span className="text-sm text-gray-400 font-mono">{form.accentColor}</span>
                </div>
              </div>
            </div>

            {/* Preview */}
            <div
              className="h-20 rounded-xl flex items-center justify-center text-white font-bold text-sm transition-all"
              style={{ background: `linear-gradient(135deg, ${form.heroColor}, ${form.accentColor})` }}
            >
              {form.name || 'Your Store'} — Color Preview
            </div>
          </div>
        </div>

        <button
          id="btn-save-settings"
          onClick={handleSave}
          disabled={saving}
          className="btn-press w-full py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold flex items-center justify-center gap-2 hover:opacity-90 transition-all disabled:opacity-60"
        >
          {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <Check className="w-4 h-4" />}
          {saving ? 'Saving...' : 'Save Settings'}
        </button>
      </div>
    </div>
  );
}
