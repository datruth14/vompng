const CACHE = 'vomp-v1';
const STATIC = [
  '/assets/theme.css',
  '/assets/img/logo.png',
  '/assets/img/icon-192.png',
  '/assets/img/icon-512.png',
  '/manifest.json'
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE).then((c) => c.addAll(STATIC)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) => Promise.all(keys.map((k) => { if (k !== CACHE) return caches.delete(k); }))).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  const { method, url } = e.request;
  const u = new URL(url);

  // Never cache API POSTs or admin endpoints
  if (method === 'POST' || u.pathname.startsWith('/api/') || u.pathname.startsWith('/admin/')) {
    return;
  }

  // Static assets: cache first
  if (STATIC.some((s) => url.endsWith(s)) || /\.(css|js|png|jpg|jpeg|gif|svg|woff2?|ico|webp)$/.test(u.pathname)) {
    e.respondWith(
      caches.match(e.request).then((cached) => cached || fetch(e.request).then((res) => {
        const clone = res.clone();
        caches.open(CACHE).then((c) => c.put(e.request, clone));
        return res;
      }))
    );
    return;
  }

  // Navigation / pages: network first, cache fallback
  if (method === 'GET' && u.origin === self.location.origin) {
    e.respondWith(
      fetch(e.request).then((res) => {
        const clone = res.clone();
        caches.open(CACHE).then((c) => c.put(e.request, clone));
        return res;
      }).catch(() => caches.match(e.request).then((cached) => cached || caches.match('/')))
    );
  }
});
