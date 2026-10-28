// Versi cache mengikuti query string saat registrasi: /sw.js?v=<build>
// Dengan begitu setiap build baru otomatis memakai cache baru tanpa edit file ini.
const VERSION = new URL(self.location.href).searchParams.get('v') || 'dev';
const CACHE_NAME = `sim-pimpinan-cache-${VERSION}`;
const urlsToCache = [
  '/manifest.json'
  // Gambar logo sengaja dihapus dari cache default biar selalu fresh
];

self.addEventListener('install', event => {
  // Langsung aktifkan SW baru tanpa nunggu
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(cacheNames => Promise.all(
        cacheNames
          .filter(name => name !== CACHE_NAME)
          .map(name => caches.delete(name))
      ))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  // Hanya proses request GET same-origin
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;

  // Network-first: HTML dan asset selalu diambil terbaru,
  // cache hanya dipakai sebagai fallback saat offline.
  event.respondWith(
    fetch(event.request)
      .catch(() => caches.match(event.request))
      .then(cached => cached || Response.error())
  );
});
