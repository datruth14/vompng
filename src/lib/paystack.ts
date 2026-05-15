export const PLANS = {
  starter: {
    name: 'Starter Plan',
    tokens: 500,
    amount: 400000, // in kobo (₦4,000)
    price: '₦4,000',
  },
  pro: {
    name: 'Pro Plan',
    tokens: 1000,
    amount: 700000, // in kobo (₦7,000)
    price: '₦7,000',
  },
} as const;

export type PlanKey = keyof typeof PLANS;

export async function initializePaystackTransaction({
  email,
  amount,
  metadata,
  callbackUrl,
}: {
  email: string;
  amount: number;
  metadata: Record<string, unknown>;
  callbackUrl: string;
}) {
  const response = await fetch('https://api.paystack.co/transaction/initialize', {
    method: 'POST',
    headers: {
      Authorization: `Bearer ${process.env.PAYSTACK_SECRET_KEY}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email,
      amount,
      metadata,
      callback_url: callbackUrl,
    }),
  });

  const data = await response.json();
  if (!data.status) throw new Error(data.message ?? 'Paystack initialization failed');
  return data.data as { authorization_url: string; access_code: string; reference: string };
}

export async function verifyPaystackTransaction(reference: string) {
  const response = await fetch(
    `https://api.paystack.co/transaction/verify/${reference}`,
    {
      headers: {
        Authorization: `Bearer ${process.env.PAYSTACK_SECRET_KEY}`,
      },
    }
  );
  const data = await response.json();
  if (!data.status) throw new Error(data.message ?? 'Paystack verification failed');
  return data.data as {
    status: string;
    amount: number;
    metadata: Record<string, unknown>;
    reference: string;
  };
}

export function verifyPaystackWebhookSignature(
  payload: string,
  signature: string
): boolean {
  const crypto = require('crypto');
  const hash = crypto
    .createHmac('sha512', process.env.PAYSTACK_SECRET_KEY!)
    .update(payload)
    .digest('hex');
  return hash === signature;
}
