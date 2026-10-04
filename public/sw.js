const CACHE_NAME = 'sim-pimpinan-cache-v2';
const urlsToCache = [
  '/',
  '/manifest.json'
  // Gambar logo sengaja dihapus dari cache default biar selalu fresh
];

self.addEventListener('install', event => {
  // Langsung aktifkan SW baru tanpa nunggu
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(name => name !== CACHE_NAME).map(name => caches.delete(name))
      );
    }).then(() => {
      return self.clients.claim();
    })
  );
});

self.addEventListener('fetch', event => {
  // Hanya proses request GET
  if (event.request.method !== 'GET') return;

  event.respondWith(
    fetch(event.request)
      .catch(() => {
        // Kalau offline atau error jaringan, baru ambil dari cache yang di-install di awal
        return caches.match(event.request);
      })
  );
});
