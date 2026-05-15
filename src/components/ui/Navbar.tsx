'use client';

import Link from 'next/link';
import { useState, useEffect } from 'react';
import { Menu, X, Zap, ChevronRight } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';

export default function Navbar() {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 20);
    window.addEventListener('scroll', onScroll);
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <nav
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${
        scrolled 
          ? 'glass-morphism-strong py-4' 
          : 'bg-transparent py-10'
      }`}
    >
      <div className="max-w-7xl mx-auto px-8 flex items-center justify-between">
        <Link href="/" className="flex items-center gap-3 group">
          <div className="relative">
            <div className="absolute inset-0 bg-indigo-500 blur-lg opacity-40 group-hover:opacity-70 transition-opacity" />
            <div className="relative w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform duration-300">
              <Zap className="w-5 h-5 text-white fill-current" />
            </div>
          </div>
          <span className="text-2xl font-black tracking-tight text-white">VOMP</span>
        </Link>

        {/* Desktop nav */}
        <div className="hidden md:flex items-center gap-10">
          {['Features', 'Pricing', 'How It Works'].map((item) => (
            <a 
              key={item} 
              href={`#${item.toLowerCase().replace(/\s+/g, '-')}`} 
              className="text-sm font-medium text-gray-400 hover:text-white transition-colors relative group"
            >
              {item}
              <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-indigo-500 transition-all group-hover:w-full" />
            </a>
          ))}
        </div>

        <div className="hidden md:flex items-center gap-6">
          <Link
            href="/login"
            className="text-sm font-medium text-gray-400 hover:text-white transition-colors"
          >
            Sign In
          </Link>
          <Link
            href="/onboarding"
            className="btn-press group relative overflow-hidden px-6 py-3 rounded-full bg-indigo-600 text-white font-bold text-sm shadow-xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all"
          >
            <span className="relative z-10">Get Started Free</span>
            <div className="absolute inset-0 bg-gradient-to-r from-indigo-400 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
          </Link>
        </div>

        {/* Mobile menu button */}
        <button
          className="md:hidden w-10 h-10 rounded-lg glass-morphism flex items-center justify-center text-gray-400 hover:text-white transition-all"
          onClick={() => setMobileOpen(!mobileOpen)}
        >
          {mobileOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
        </button>
      </div>

      {/* Mobile menu */}
      <AnimatePresence>
        {mobileOpen && (
          <motion.div 
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className="md:hidden absolute top-full left-0 right-0 glass-morphism-strong border-t border-white/10 px-6 py-8 space-y-6 flex flex-col items-center"
          >
            {['Features', 'Pricing', 'How It Works'].map((item) => (
              <a 
                key={item} 
                href={`#${item.toLowerCase().replace(/\s+/g, '-')}`} 
                className="text-lg font-medium text-gray-300"
                onClick={() => setMobileOpen(false)}
              >
                {item}
              </a>
            ))}
            <div className="w-full h-px bg-white/10" />
            <Link href="/login" className="text-gray-300 font-medium">Sign In</Link>
            <Link 
              href="/onboarding" 
              className="w-full text-center py-4 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold"
              onClick={() => setMobileOpen(false)}
            >
              Get Started Free
            </Link>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  );
}
