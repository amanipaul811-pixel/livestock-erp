const CACHE_VERSION = 'livestock-erp-v1';
const APP_SHELL = [
    '/manifest.json',
    '/offline.html',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => cache.addAll(APP_SHELL))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== CACHE_VERSION).map((key) => caches.delete(key))
        ))
    );
    self.clients.claim();
});

// Network-first for same-origin GET requests: prefer a fresh response, cache
// it for next time, and fall back to the cache (or the offline page for a
// page navigation) when the network is unavailable. This gives fast reloads
// of already-visited pages plus a friendly offline state -- not full offline
// CRUD, which would need background sync and is a bigger, separate feature.
self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET' || new URL(request.url).origin !== self.location.origin) {
        return;
    }

    event.respondWith(
        fetch(request)
            .then((response) => {
                const copy = response.clone();
                caches.open(CACHE_VERSION).then((cache) => cache.put(request, copy));
                return response;
            })
            .catch(async () => {
                const cached = await caches.match(request);
                if (cached) {
                    return cached;
                }
                if (request.mode === 'navigate') {
                    return caches.match('/offline.html');
                }
                return Response.error();
            })
    );
});
