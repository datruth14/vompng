'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { createClient } from '@/utils/supabase/client';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Zap,
  Store,
  User,
  ArrowRight,
  ArrowLeft,
  Check,
  Loader2,
  Eye,
  EyeOff,
  Phone,
  Mail,
  Lock,
  ShoppingBag,
  AlignLeft,
} from 'lucide-react';

const STEPS = [
  { id: 1, title: 'Account', icon: User },
  { id: 2, title: 'Store', icon: Store },
  { id: 3, title: 'Live', icon: Zap },
];

interface FormData {
  name: string;
  email: string;
  password: string;
  storeName: string;
  storeDescription: string;
  contactPhone: string;
}

export default function OnboardingPage() {
  const router = useRouter();
  const supabase = createClient();
  const [step, setStep] = useState(1);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [storeSlug, setStoreSlug] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [origin, setOrigin] = useState('');

  useEffect(() => {
    setOrigin(window.location.origin);
  }, []);

  const [form, setForm] = useState<FormData>({
    name: '',
    email: '',
    password: '',
    storeName: '',
    storeDescription: '',
    contactPhone: '',
  });

  const update = (field: keyof FormData, value: string) =>
    setForm((prev) => ({ ...prev, [field]: value }));

  const previewSlug = form.storeName
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '');

  const validateStep1 = () => {
    if (!form.name.trim()) return 'Please enter your full name';
    if (!form.email.trim() || !form.email.includes('@')) return 'Please enter a valid email';
    if (form.password.length < 6) return 'Password must be at least 6 characters';
    return '';
  };

  const validateStep2 = () => {
    if (!form.storeName.trim()) return 'Please enter your store name';
    if (!form.contactPhone.trim()) return 'Please enter your WhatsApp number';
    return '';
  };

  const handleNext = () => {
    setError('');
    if (step === 1) {
      const err = validateStep1();
      if (err) { setError(err); return; }
    }
    if (step === 2) {
      const err = validateStep2();
      if (err) { setError(err); return; }
    }
    setStep((s) => s + 1);
  };

  const handleSubmit = async () => {
    setLoading(true);
    setError('');
    try {
      const res = await fetch('/api/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form),
      });
      const data = await res.json();
      if (!res.ok) {
        setError(data.error ?? 'Registration failed');
        setLoading(false);
        return;
      }
      setStoreSlug(data.storeSlug);

      const { error: signInError } = await supabase.auth.signInWithPassword({
        email: form.email,
        password: form.password,
      });

      if (signInError) {
        setError('Registration successful but login failed. Please try logging in manually.');
        setLoading(false);
        return;
      }

      setStep(3);
    } catch {
      setError('Something went wrong. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-950 flex items-center justify-center px-4 py-12 selection:bg-indigo-500/30">
      {/* Background Blobs */}
      <div className="fixed inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] animate-blob" />
        <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] animate-blob" style={{ animationDelay: '2s' }} />
      </div>

      <div className="relative z-10 w-full max-w-lg">
        {/* Logo */}
        <div className="text-center mb-10">
          <Link href="/" className="inline-flex items-center gap-3 group">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform duration-300">
              <Zap className="w-5 h-5 text-white fill-current" />
            </div>
            <span className="text-3xl font-black text-white tracking-tight">VOMP</span>
          </Link>
        </div>

        {/* Step indicator */}
        {step < 3 && (
          <div className="flex items-center justify-center gap-3 mb-10">
            {STEPS.map((s, i) => (
              <div key={s.id} className="flex items-center gap-3">
                <div
                  className={`flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-500 border ${
                    step === s.id
                      ? 'bg-indigo-500 text-white border-indigo-400 shadow-lg shadow-indigo-500/20'
                      : step > s.id
                      ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                      : 'bg-white/5 text-gray-500 border-white/5'
                  }`}
                >
                  {step > s.id ? <Check className="w-3.5 h-3.5" /> : <s.icon className="w-3.5 h-3.5" />}
                  <span className="hidden sm:inline">{s.title}</span>
                </div>
                {i < STEPS.length - 1 && <div className={`w-6 h-px ${step > s.id + 1 ? 'bg-emerald-500/30' : 'bg-white/5'}`} />}
              </div>
            ))}
          </div>
        )}

        {/* Form Container */}
        <div className="glass-morphism rounded-[3rem] p-12 md:p-16 border border-white/10 relative overflow-hidden">
          <div className="absolute top-0 right-0 w-48 h-48 bg-indigo-500/5 blur-[80px] rounded-full" />
          
          <AnimatePresence mode="wait">
            {/* Step 1: Account */}
            {step === 1 && (
              <motion.div
                key="step1"
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -20 }}
                className="space-y-10"
              >
                <div>
                  <h2 className="text-4xl font-black text-white mb-3 tracking-tight">Join the future.</h2>
                  <p className="text-gray-400 font-medium text-lg">Create your seller account in seconds.</p>
                </div>

                <div className="space-y-8">
                  <div className="group">
                    <label className="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Full Name</label>
                    <div className="relative">
                      <User className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-indigo-400 transition-colors" />
                      <input
                        id="input-name"
                        type="text"
                        value={form.name}
                        onChange={(e) => update('name', e.target.value)}
                        placeholder="Amara Okafor"
                        className="w-full bg-white/5 border border-white/5 rounded-2xl pl-12 pr-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all"
                      />
                    </div>
                  </div>

                  <div className="group">
                    <label className="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Email Address</label>
                    <div className="relative">
                      <Mail className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-indigo-400 transition-colors" />
                      <input
                        id="input-email"
                        type="email"
                        value={form.email}
                        onChange={(e) => update('email', e.target.value)}
                        placeholder="you@example.com"
                        className="w-full bg-white/5 border border-white/5 rounded-2xl pl-12 pr-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all"
                      />
                    </div>
                  </div>

                  <div className="group">
                    <label className="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Password</label>
                    <div className="relative">
                      <Lock className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-indigo-400 transition-colors" />
                      <input
                        id="input-password"
                        type={showPassword ? 'text' : 'password'}
                        value={form.password}
                        onChange={(e) => update('password', e.target.value)}
                        placeholder="••••••••"
                        className="w-full bg-white/5 border border-white/5 rounded-2xl pl-12 pr-12 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all"
                      />
                      <button
                        type="button"
                        onClick={() => setShowPassword(!showPassword)}
                        className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors"
                      >
                        {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                      </button>
                    </div>
                  </div>
                </div>

                {error && (
                  <motion.p initial={{ opacity: 0 }} animate={{ opacity: 1 }} className="text-rose-400 text-sm font-bold bg-rose-500/10 p-3 rounded-xl border border-rose-500/20">
                    {error}
                  </motion.p>
                )}

                <button
                  id="btn-next-step1"
                  onClick={handleNext}
                  className="btn-press w-full py-5 rounded-2xl bg-indigo-500 text-white font-black text-lg shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all flex items-center justify-center gap-3"
                >
                  Create Account
                  <ArrowRight className="w-5 h-5" />
                </button>

                <p className="text-center text-sm font-medium text-gray-500">
                  Already a seller? <Link href="/login" className="text-indigo-400 hover:text-indigo-300">Sign in here</Link>
                </p>
              </motion.div>
            )}

            {/* Step 2: Store Details */}
            {step === 2 && (
              <motion.div
                key="step2"
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -20 }}
                className="space-y-12"
              >
                <div>
                  <h2 className="text-4xl font-black text-white mb-4 tracking-tight">Tell us about your shop.</h2>
                  <p className="text-gray-400 font-medium text-lg">Your brand, your rules.</p>
                </div>

                <div className="space-y-10">
                  <div className="group">
                    <label className="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-3 ml-1">Store Name</label>
                    <div className="relative">
                      <Store className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-indigo-400 transition-colors" />
                      <input
                        id="input-store-name"
                        type="text"
                        value={form.storeName}
                        onChange={(e) => update('storeName', e.target.value)}
                        placeholder="e.g. Amara's Gems"
                        className="w-full bg-white/5 border border-white/5 rounded-2xl pl-12 pr-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all"
                      />
                    </div>
                    {previewSlug && (
                      <div className="mt-3 p-3 rounded-xl bg-indigo-500/5 border border-indigo-500/10">
                        <p className="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-1">Preview URL</p>
                        <p className="text-indigo-400 font-mono text-xs truncate">vomp.app/store/{previewSlug}</p>
                      </div>
                    )}
                  </div>

                  <div className="group">
                    <label className="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">Store Bio</label>
                    <div className="relative">
                      <AlignLeft className="absolute left-4 top-4 w-5 h-5 text-gray-500 group-focus-within:text-indigo-400 transition-colors" />
                      <textarea
                        id="input-store-desc"
                        value={form.storeDescription}
                        onChange={(e) => update('storeDescription', e.target.value)}
                        placeholder="Describe your magic..."
                        rows={3}
                        className="w-full bg-white/5 border border-white/5 rounded-2xl pl-12 pr-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all resize-none"
                      />
                    </div>
                  </div>

                  <div className="group">
                    <label className="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 ml-1">WhatsApp Number</label>
                    <div className="relative">
                      <Phone className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-indigo-400 transition-colors" />
                      <input
                        id="input-phone"
                        type="tel"
                        value={form.contactPhone}
                        onChange={(e) => update('contactPhone', e.target.value)}
                        placeholder="+234 800 000 0000"
                        className="w-full bg-white/5 border border-white/5 rounded-2xl pl-12 pr-4 py-4 text-white placeholder-gray-600 focus:outline-none focus:border-indigo-500/50 focus:bg-white/[0.08] transition-all"
                      />
                    </div>
                  </div>
                </div>

                {error && (
                  <p className="text-rose-400 text-sm font-bold bg-rose-500/10 p-3 rounded-xl border border-rose-500/20">
                    {error}
                  </p>
                )}

                <div className="flex gap-4 pt-2">
                  <button
                    onClick={() => setStep(1)}
                    className="btn-press flex-1 py-5 rounded-2xl bg-white/5 border border-white/5 text-gray-300 font-bold hover:bg-white/10 transition-all flex items-center justify-center gap-2"
                  >
                    <ArrowLeft className="w-5 h-5" />
                    Back
                  </button>
                  <button
                    id="btn-create-store"
                    onClick={handleSubmit}
                    disabled={loading}
                    className="btn-press flex-[2] py-5 rounded-2xl bg-indigo-500 text-white font-black text-lg shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all disabled:opacity-60 flex items-center justify-center gap-3"
                  >
                    {loading ? (
                      <><Loader2 className="w-5 h-5 animate-spin" /> Launching...</>
                    ) : (
                      <><ShoppingBag className="w-5 h-5" /> Go Live!</>
                    )}
                  </button>
                </div>
              </motion.div>
            )}

            {/* Step 3: Success */}
            {step === 3 && (
              <motion.div
                key="step3"
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                className="text-center space-y-8 py-4"
              >
                <div className="relative w-24 h-24 mx-auto">
                  <div className="absolute inset-0 bg-emerald-500 blur-2xl opacity-40 animate-pulse" />
                  <div className="relative w-full h-full rounded-full bg-gradient-to-br from-emerald-400 to-green-600 flex items-center justify-center shadow-xl">
                    <Check className="w-12 h-12 text-white" strokeWidth={4} />
                  </div>
                </div>
                
                <div>
                  <h2 className="text-4xl font-black text-white mb-3">You're in! 🎉</h2>
                  <p className="text-gray-400 font-medium">
                    We've credited <span className="text-emerald-400 font-bold">50 free tokens</span> to your account to get those first orders rolling.
                  </p>
                </div>

                <div className="p-6 rounded-[2rem] bg-indigo-500/10 border border-indigo-500/20 text-left">
                  <p className="text-[10px] uppercase tracking-widest text-gray-500 font-black mb-2">Your Public Store Link</p>
                  <div className="flex items-center justify-between gap-4">
                    <p className="text-indigo-400 font-mono text-sm truncate font-bold">{origin}/store/{storeSlug}</p>
                    <button className="text-[10px] uppercase font-black text-indigo-300 hover:text-white transition-colors">Copy</button>
                  </div>
                </div>

                <div className="grid grid-cols-1 gap-4">
                  <button
                    id="btn-goto-dashboard"
                    onClick={() => router.push(`/dashboard/${storeSlug}`)}
                    className="btn-press py-5 rounded-2xl bg-indigo-500 text-white font-black text-lg shadow-xl shadow-indigo-500/20 hover:bg-indigo-400 transition-all flex items-center justify-center gap-3"
                  >
                    Go to Dashboard
                    <ArrowRight className="w-5 h-5" />
                  </button>
                  <button
                    id="btn-goto-store"
                    onClick={() => router.push(`/store/${storeSlug}`)}
                    className="btn-press py-5 rounded-2xl bg-white/5 border border-white/5 text-gray-300 font-bold hover:bg-white/10 transition-all"
                  >
                    Preview My Store
                  </button>
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </div>

        <p className="text-center mt-10 text-gray-600 text-xs font-bold uppercase tracking-widest">
          Secure and reliable commerce by <span className="text-gray-400">VOMP Technology</span>
        </p>
      </div>
    </div>
  );
}
