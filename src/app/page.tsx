'use client';

import Link from 'next/link';
import Navbar from '@/components/ui/Navbar';
import { motion, easeOut } from 'framer-motion';
import {
  Store,
  ShoppingCart,
  Zap,
  Shield,
  Globe,
  MessageCircle,
  ArrowRight,
  Check,
  Star,
  TrendingUp,
  Smartphone,
  Play,
} from 'lucide-react';

const fadeInUp = {
  initial: { opacity: 0, y: 30 },
  animate: { opacity: 1, y: 0 },
  transition: { duration: 0.6, ease: easeOut }
};

const staggerContainer = {
  animate: {
    transition: {
      staggerChildren: 0.1
    }
  }
};

const features = [
  {
    icon: Globe,
    title: 'Instant Store URL',
    desc: 'Launch your personalized shop at your-name.vomp.shop in 60 seconds.',
    color: 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
  },
  {
    icon: MessageCircle,
    title: 'WhatsApp Commerce',
    desc: 'Direct order notifications to your WhatsApp — no more abandoned carts.',
    color: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
  },
  {
    icon: Smartphone,
    title: 'Mobile-First Design',
    desc: 'A seamless shopping experience optimized for your customers\' devices.',
    color: 'bg-purple-500/10 text-purple-400 border-purple-500/20',
  },
  {
    icon: Play,
    title: 'Video Showcase',
    desc: 'Bring products to life with video integration for higher conversion.',
    color: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
  },
  {
    icon: Shield,
    title: 'Pay-per-Order',
    desc: 'Token-based billing ensures you only pay when customers actually click.',
    color: 'bg-sky-500/10 text-sky-400 border-sky-500/20',
  },
  {
    icon: TrendingUp,
    title: 'Business Analytics',
    desc: 'Track your growth, manage inventory, and optimize your sales flow.',
    color: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
  },
];

const plans = [
  {
    name: 'Free Starter',
    price: '₦0',
    tokens: 50,
    features: ['50 Order Tokens', 'Unlimited Products', 'Custom Color Theme', 'Standard Layout'],
    highlight: false,
    cta: 'Get Started',
  },
  {
    name: 'Scale Plan',
    price: '₦4,000',
    tokens: 500,
    features: ['500 Order Tokens', 'Everything in Free', 'Product Video Support', 'Priority Support', 'Verified Badge'],
    highlight: true,
    cta: 'Buy Starter',
  },
  {
    name: 'Empire Plan',
    price: '₦7,000',
    tokens: 1000,
    features: ['1000 Order Tokens', 'Everything in Scale', 'Advanced Analytics', 'Custom SEO Config', 'Bulk Product Import'],
    highlight: false,
    cta: 'Go Pro',
  },
];

export default function LandingPage() {
  return (
    <div className="relative min-h-screen selection:bg-indigo-500/30">
      <Navbar />

      {/* Hero Section */}
      <section className="relative pt-40 pb-24 lg:pt-60 lg:pb-40 px-6 overflow-hidden">
        {/* Background Decorative Elements */}
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full pointer-events-none -z-10">
          <div className="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-600/20 rounded-full blur-[140px] animate-blob" />
          <div className="absolute bottom-0 left-0 w-[600px] h-[600px] bg-purple-600/20 rounded-full blur-[140px] animate-blob" style={{ animationDelay: '2.5s' }} />
        </div>

        <div className="max-w-6xl mx-auto flex flex-col items-center text-center">
          <motion.div 
            {...fadeInUp}
            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full glass-morphism border-white/10 text-sm font-bold text-indigo-300 mb-12"
          >
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
            </span>
            Trusted by 5,000+ local vendors
          </motion.div>

          <motion.h1 
            initial={{ opacity: 0, y: 40 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.1 }}
            className="text-6xl md:text-8xl lg:text-9xl font-black tracking-tight mb-10 leading-[1.05]"
          >
            Your Store. <br />
            <span className="text-gradient">No Commisions.</span> <br />
            Just Orders.
          </motion.h1>

          <motion.p 
            initial={{ opacity: 0, y: 40 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="max-w-3xl text-xl md:text-2xl text-gray-400 mb-16 leading-relaxed px-4"
          >
            Empower your business with a stunning storefront. 
            Customers shop on your link, you receive orders on WhatsApp. 
            Simple, fast, and built for growth.
          </motion.p>

          <motion.div 
            initial={{ opacity: 0, y: 40 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.3 }}
            className="flex flex-col sm:flex-row gap-6 mb-24"
          >
            <Link
              href="/onboarding"
              className="btn-press group relative flex items-center gap-4 px-12 py-6 rounded-full bg-white text-gray-950 font-black text-xl transition-all hover:scale-105 active:scale-95 shadow-2xl shadow-white/10"
            >
              Start Selling Free
              <ArrowRight className="w-6 h-6 group-hover:translate-x-1 transition-transform" />
            </Link>
            <Link
              href="#features"
              className="btn-press px-12 py-6 rounded-full glass-morphism text-white font-bold text-xl hover:bg-white/10 transition-all border border-white/10 shadow-xl"
            >
              Explore Features
            </Link>
          </motion.div>

          {/* Visual Showcase */}
          <motion.div 
            initial={{ opacity: 0, y: 120 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 1.2, delay: 0.4 }}
            className="relative w-full max-w-6xl group px-4"
          >
            <div className="absolute inset-0 bg-indigo-600/20 blur-[120px] opacity-60 group-hover:opacity-90 transition-opacity pointer-events-none" />
            <div className="relative glass-morphism rounded-[3rem] p-6 border-white/10 shadow-2xl overflow-hidden aspect-video">
              <div className="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 pointer-events-none" />
              {/* Fake UI Content */}
              <div className="w-full h-full flex items-center justify-center bg-gray-900/50 rounded-[2.5rem] overflow-hidden">
                <div className="grid grid-cols-3 gap-10 p-12 w-full h-full">
                  {[1, 2, 3].map((i) => (
                    <div key={i} className="rounded-3xl bg-white/5 border border-white/5 animate-pulse overflow-hidden">
                      <div className="h-2/3 bg-white/10" />
                      <div className="p-6 space-y-3">
                        <div className="h-5 w-2/3 bg-white/10 rounded" />
                        <div className="h-4 w-1/3 bg-white/10 rounded" />
                      </div>
                    </div>
                  ))}
                </div>
              </div>
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="w-24 h-24 rounded-full bg-white text-gray-950 flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform cursor-pointer border-[8px] border-white/20">
                  <Play className="w-10 h-10 fill-current translate-x-1" />
                </div>
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Features Grid */}
      <section id="features" className="py-40 px-6 relative">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-32">
            <motion.h2 
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="text-5xl md:text-7xl font-black mb-8"
            >
              Designed for <br />
              <span className="text-gradient">Modern Sellers</span>
            </motion.h2>
            <p className="text-gray-400 text-xl max-w-2xl mx-auto leading-relaxed">
              Skip the complex setups. VOMP provides everything you need to start making sales from day one.
            </p>
          </div>

          <motion.div 
            variants={staggerContainer}
            initial="initial"
            whileInView="animate"
            viewport={{ once: true }}
            className="grid md:grid-cols-2 lg:grid-cols-3 gap-12"
          >
            {features.map((feature, i) => (
              <motion.div
                key={i}
                variants={fadeInUp}
                className="group p-12 rounded-[3rem] glass-morphism hover:bg-white/5 transition-all duration-500 border border-white/5 relative overflow-hidden"
              >
                <div className="absolute top-0 right-0 w-32 h-32 bg-white/5 blur-3xl rounded-full translate-x-10 -translate-y-10 group-hover:scale-150 transition-transform duration-700" />
                <div className={`w-16 h-16 rounded-2xl flex items-center justify-center mb-10 border ${feature.color} group-hover:scale-110 transition-transform duration-500 shadow-xl`}>
                  <feature.icon className="w-8 h-8" />
                </div>
                <h3 className="text-3xl font-black mb-6 text-white">{feature.title}</h3>
                <p className="text-gray-400 text-lg leading-relaxed">{feature.desc}</p>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </section>

      {/* Stats Counter */}
      <section className="py-32 border-y border-white/5 bg-white/[0.02]">
        <div className="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-16">
          {[
            { label: 'Orders Processed', value: '250K+' },
            { label: 'Active Stores', value: '5K+' },
            { label: 'Total Revenue', value: '₦120M+' },
            { label: 'Daily Clicks', value: '15K+' },
          ].map((stat, i) => (
            <div key={i} className="text-center">
              <div className="text-5xl md:text-6xl font-black text-white mb-4 tracking-tighter">{stat.value}</div>
              <div className="text-xs font-black text-gray-500 uppercase tracking-[0.2em]">{stat.label}</div>
            </div>
          ))}
        </div>
      </section>

      {/* Pricing Section */}
      <section id="pricing" className="py-40 px-6">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-32">
            <h2 className="text-5xl md:text-7xl font-black mb-8">Simple, <br /><span className="text-gradient">Honest Pricing</span></h2>
            <p className="text-gray-400 text-xl max-w-2xl mx-auto leading-relaxed">No monthly fees. No commissions. Just buy tokens when you need them.</p>
          </div>

          <div className="grid md:grid-cols-3 gap-10">
            {plans.map((plan, i) => (
              <motion.div
                key={i}
                initial={{ opacity: 0, y: 40 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1, duration: 0.6 }}
                className={`relative p-12 rounded-[3.5rem] transition-all duration-500 group ${
                  plan.highlight 
                    ? 'bg-gradient-to-b from-indigo-600/20 to-transparent border-2 border-indigo-500/50 glow-primary' 
                    : 'glass-morphism border-white/5 hover:border-white/20'
                }`}
              >
                {plan.highlight && (
                  <div className="absolute -top-6 left-1/2 -translate-x-1/2 px-8 py-2.5 rounded-full bg-indigo-500 text-white font-black text-sm uppercase tracking-widest shadow-2xl z-20">
                    Most Popular
                  </div>
                )}
                
                <div className="mb-12">
                  <div className="text-xl font-bold text-gray-400 mb-4 tracking-wider uppercase">{plan.name}</div>
                  <div className="text-7xl font-black text-white mb-6 tracking-tighter">{plan.price}</div>
                  <div className="inline-flex items-center gap-3 px-4 py-2 rounded-xl bg-indigo-500/10 text-indigo-400 font-black text-sm">
                    <Zap className="w-5 h-5 fill-current" />
                    {plan.tokens} Order Tokens
                  </div>
                </div>

                <div className="space-y-6 mb-16">
                  {plan.features.map((feature, j) => (
                    <div key={j} className="flex items-start gap-4 text-gray-300">
                      <div className="w-6 h-6 rounded-full bg-indigo-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <Check className="w-3.5 h-3.5 text-indigo-400" />
                      </div>
                      <span className="text-base font-medium leading-tight">{feature}</span>
                    </div>
                  ))}
                </div>

                <Link
                  href="/onboarding"
                  className={`btn-press w-full py-6 rounded-[2rem] font-black text-lg text-center transition-all shadow-2xl ${
                    plan.highlight 
                      ? 'bg-indigo-500 text-white hover:bg-indigo-400 shadow-indigo-500/20' 
                      : 'glass-morphism border-white/10 text-white hover:bg-white/10'
                  }`}
                >
                  {plan.cta}
                </Link>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Banner */}
      <section className="py-40 px-6">
        <div className="max-w-7xl mx-auto">
          <motion.div 
            initial={{ opacity: 0, scale: 0.95 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            transition={{ duration: 0.8 }}
            className="relative rounded-[4rem] overflow-hidden p-20 md:p-32 text-center bg-indigo-600 shadow-[0_40px_100px_-20px_rgba(79,70,229,0.5)]"
          >
            <div className="absolute inset-0 bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-700" />
            <div className="absolute top-0 right-0 w-[800px] h-[800px] bg-white/10 rounded-full blur-[140px] -translate-y-1/2 translate-x-1/2" />
            
            <div className="relative z-10 max-w-4xl mx-auto">
              <h2 className="text-6xl md:text-8xl font-black text-white mb-10 leading-[1.1] tracking-tight">Ready to grow your business?</h2>
              <p className="text-2xl text-indigo-50 font-medium mb-16 opacity-90 leading-relaxed max-w-2xl mx-auto">Join 5,000+ vendors who are already selling the smart way. No tech skills required.</p>
              <Link
                href="/onboarding"
                className="btn-press inline-flex items-center gap-5 px-14 py-7 rounded-full bg-white text-indigo-600 font-black text-2xl shadow-[0_20px_50px_rgba(255,255,255,0.2)] hover:scale-105 transition-all"
              >
                Create My Store Now
                <ArrowRight className="w-8 h-8" />
              </Link>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-32 border-t border-white/5 bg-black">
        <div className="max-w-7xl mx-auto px-6">
          <div className="flex flex-col md:flex-row justify-between items-center gap-20">
            <div className="flex flex-col items-center md:items-start">
              <Link href="/" className="flex items-center gap-4 mb-8 group">
                <div className="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-indigo-500/20">
                  <Zap className="w-6 h-6 text-white fill-current" />
                </div>
                <span className="text-3xl font-black text-white tracking-tight">VOMP</span>
              </Link>
              <p className="text-gray-500 text-lg font-medium text-center md:text-left leading-relaxed max-w-xs">
                The modern commerce platform built for local vendors.
              </p>
            </div>

            <div className="flex gap-24 text-base">
              <div className="space-y-6">
                <div className="font-black text-white uppercase tracking-widest text-xs">Platform</div>
                <div className="space-y-4">
                  <a href="#features" className="block text-gray-500 hover:text-white transition-colors">Features</a>
                  <a href="#pricing" className="block text-gray-500 hover:text-white transition-colors">Pricing</a>
                </div>
              </div>
              <div className="space-y-6">
                <div className="font-black text-white uppercase tracking-widest text-xs">Legal</div>
                <div className="space-y-4">
                  <a href="#" className="block text-gray-500 hover:text-white transition-colors">Privacy Policy</a>
                  <a href="#" className="block text-gray-500 hover:text-white transition-colors">Terms of Service</a>
                </div>
              </div>
            </div>
          </div>
          <div className="mt-32 pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-10">
            <p className="text-gray-600 text-sm font-medium tracking-wide">© 2025 VOMP Technology. Crafted with ❤️ in Lagos.</p>
            <div className="flex gap-10">
              {[ShoppingCart, MessageCircle, Globe].map((Icon, i) => (
                <a key={i} href="#" className="text-gray-600 hover:text-indigo-400 transition-all hover:scale-125">
                  <Icon className="w-6 h-6" />
                </a>
              ))}
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
