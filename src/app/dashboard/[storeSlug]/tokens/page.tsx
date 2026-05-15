'use client';

import { useState, useEffect } from 'react';
import { useParams, useSearchParams } from 'next/navigation';
import { Coins, Zap, Check, ArrowRight, Loader2, History, TrendingUp, TrendingDown } from 'lucide-react';

const PLANS = [
  {
    key: 'starter',
    name: 'Starter',
    price: '₦4,000',
    tokens: 500,
    desc: 'Great for growing stores',
    popular: true,
    features: ['500 WhatsApp order clicks', 'Never expires', 'Stacks with existing balance'],
  },
  {
    key: 'pro',
    name: 'Pro',
    price: '₦7,000',
    tokens: 1000,
    desc: 'For high-volume sellers',
    popular: false,
    features: ['1,000 WhatsApp order clicks', 'Never expires', 'Best value per token'],
  },
];

interface TokenTx {
  _id: string;
  type: 'credit' | 'debit';
  amount: number;
  description: string;
  createdAt: string;
}

export default function TokensPage() {
  const params = useParams();
  const searchParams = useSearchParams();
  const storeSlug = params.storeSlug as string;

  const [balance, setBalance] = useState<number | null>(null);
  const [transactions, setTransactions] = useState<TokenTx[]>([]);
  const [loading, setLoading] = useState(true);
  const [purchasing, setPurchasing] = useState<string | null>(null);
  const [paymentSuccess, setPaymentSuccess] = useState(false);

  useEffect(() => {
    if (searchParams.get('payment') === 'success') {
      setPaymentSuccess(true);
      setTimeout(() => setPaymentSuccess(false), 5000);
    }
    fetchData();
  }, []);

  const fetchData = async () => {
    const [storeRes, txRes] = await Promise.all([
      fetch(`/api/settings?storeSlug=${storeSlug}`),
      fetch(`/api/tokens/history?storeSlug=${storeSlug}`),
    ]);
    const storeData = await storeRes.json();
    const txData = await txRes.json();
    setBalance(storeData.store?.token_balance ?? 0);
    setTransactions(txData.transactions ?? []);
    setLoading(false);
  };

  const handlePurchase = async (planKey: string) => {
    setPurchasing(planKey);
    try {
      const res = await fetch('/api/tokens/purchase', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ storeSlug, plan: planKey }),
      });
      const data = await res.json();
      if (data.authorizationUrl) {
        window.location.href = data.authorizationUrl;
      }
    } catch {
      alert('Failed to initiate payment. Please try again.');
    } finally {
      setPurchasing(null);
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
    <div className="p-8 max-w-4xl">
      <div className="mb-8">
        <h1 className="text-2xl font-black mb-1">Tokens</h1>
        <p className="text-gray-400 text-sm">
          Each token = 1 WhatsApp order redirect. Buy more to keep selling.
        </p>
      </div>

      {paymentSuccess && (
        <div className="mb-6 px-5 py-4 rounded-2xl bg-green-500/10 border border-green-500/30 text-green-400 flex items-center gap-3">
          <div className="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
            <Check className="w-4 h-4" />
          </div>
          <div>
            <p className="font-semibold text-sm">Payment successful!</p>
            <p className="text-xs text-green-500/80 mt-0.5">Your tokens will be credited within a few seconds.</p>
          </div>
        </div>
      )}

      {/* Balance card */}
      <div className="glass rounded-2xl p-7 mb-8 border border-indigo-500/20 relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-indigo-600/10 to-purple-600/10 pointer-events-none" />
        <div className="relative z-10 flex items-center justify-between">
          <div>
            <p className="text-sm text-gray-400 mb-1">Current Token Balance</p>
            <div className="flex items-baseline gap-2">
              <span className="text-5xl font-black gradient-text">{balance}</span>
              <span className="text-gray-500">tokens</span>
            </div>
            <p className="text-xs text-gray-500 mt-2">
              {balance === 0
                ? '⚠️ No tokens — customers cannot send WhatsApp orders'
                : balance! < 10
                ? '⚡ Low balance — top up soon'
                : '✅ Balance looks good'}
            </p>
          </div>
          <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center animate-float">
            <Coins className="w-8 h-8 text-white" />
          </div>
        </div>
      </div>

      {/* Plans */}
      <h2 className="font-bold text-lg mb-5">Buy More Tokens</h2>
      <div className="grid md:grid-cols-2 gap-5 mb-10">
        {PLANS.map((plan) => (
          <div
            key={plan.key}
            className={`glass rounded-2xl p-6 transition-all hover:-translate-y-1 ${
              plan.popular ? 'border-indigo-500/40 glow-purple' : 'hover:border-white/20'
            }`}
          >
            {plan.popular && (
              <span className="inline-block px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 text-xs font-semibold mb-3">
                Best Value
              </span>
            )}
            <div className="flex items-start justify-between mb-4">
              <div>
                <h3 className="font-bold text-lg">{plan.name}</h3>
                <p className="text-gray-400 text-sm">{plan.desc}</p>
              </div>
              <div className="text-right">
                <div className="text-2xl font-black">{plan.price}</div>
                <div className="text-indigo-400 text-sm font-semibold">{plan.tokens} tokens</div>
              </div>
            </div>
            <ul className="space-y-2 mb-5">
              {plan.features.map((f) => (
                <li key={f} className="flex items-center gap-2 text-sm text-gray-300">
                  <Check className="w-3.5 h-3.5 text-indigo-400 flex-shrink-0" />
                  {f}
                </li>
              ))}
            </ul>
            <button
              id={`btn-buy-${plan.key}`}
              onClick={() => handlePurchase(plan.key)}
              disabled={purchasing !== null}
              className={`btn-press w-full py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 transition-all ${
                plan.popular
                  ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:opacity-90 shadow-lg shadow-indigo-500/20'
                  : 'glass text-gray-300 hover:text-white hover:border-white/20'
              } disabled:opacity-60`}
            >
              {purchasing === plan.key ? (
                <Loader2 className="w-4 h-4 animate-spin" />
              ) : (
                <Zap className="w-4 h-4" />
              )}
              {purchasing === plan.key ? 'Redirecting...' : `Buy ${plan.tokens} Tokens`}
            </button>
          </div>
        ))}
      </div>

      {/* Transaction History */}
      <div className="glass rounded-2xl p-6">
        <h2 className="font-bold mb-5 flex items-center gap-2">
          <History className="w-4 h-4 text-indigo-400" />
          Transaction History
        </h2>
        {transactions.length === 0 ? (
          <p className="text-gray-500 text-sm text-center py-8">No transactions yet.</p>
        ) : (
          <div className="space-y-3">
            {transactions.map((tx) => (
              <div key={tx._id} className="flex items-center gap-4 py-3 border-b border-white/5 last:border-0">
                <div
                  className={`w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 ${
                    tx.type === 'credit' ? 'bg-green-500/20' : 'bg-red-500/20'
                  }`}
                >
                  {tx.type === 'credit' ? (
                    <TrendingUp className="w-4 h-4 text-green-400" />
                  ) : (
                    <TrendingDown className="w-4 h-4 text-red-400" />
                  )}
                </div>
                <div className="flex-1">
                  <p className="text-sm font-medium">{tx.description}</p>
                  <p className="text-xs text-gray-500">
                    {new Date(tx.createdAt).toLocaleDateString('en-NG', {
                      day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
                    })}
                  </p>
                </div>
                <span className={`text-sm font-bold ${tx.type === 'credit' ? 'text-green-400' : 'text-red-400'}`}>
                  {tx.type === 'credit' ? '+' : '-'}{tx.amount}
                </span>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
