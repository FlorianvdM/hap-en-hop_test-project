// Toont een eigen melding als de website niet bereikbaar is (bijvoorbeeld als de server uit staat).
// Dit werkt voor bezoekers die de website al eens hebben geopend.
const CACHE = 'hapenhop-offline-v2';
const OFFLINE_PAGINA = '/offline.html';
// Afbeeldingen die op de offline-pagina staan
const AFBEELDINGEN = ['/images/logo.png', '/favicon.ico', '/favicon-32x32.png', '/apple-touch-icon.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE)
            .then((cache) => cache.add(OFFLINE_PAGINA).then(() => Promise.all(AFBEELDINGEN.map((pad) => cache.add(pad).catch(() => {})))))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((namen) => Promise.all(namen.filter((naam) => naam !== CACHE).map((naam) => caches.delete(naam))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Logo en favicon: eerst de cache (de offline-pagina heeft ze nodig), anders het netwerk.
    // ignoreSearch, want de pagina vraagt ze op met ?v=2.
    if (url.origin === self.location.origin && AFBEELDINGEN.includes(url.pathname)) {
        event.respondWith(
            caches.match(event.request, { ignoreSearch: true }).then((gevonden) => gevonden || fetch(event.request))
        );
        return;
    }

    // Het openen van pagina's: lukt de verbinding niet, dan tonen we de offline-pagina
    if (event.request.mode !== 'navigate') {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(() => caches.match(OFFLINE_PAGINA))
    );
});