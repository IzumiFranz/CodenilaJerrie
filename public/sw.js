const CACHE_NAME = 'quiz-lms-v1';
const urlsToCache = [
    '/',
    '/css/sb-admin-2.min.css',
    '/css/student-custom.css',
    '/js/sb-admin-2.min.js',
    '/vendor/jquery/jquery.min.js',
    '/vendor/bootstrap/js/bootstrap.bundle.min.js'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => response || fetch(event.request))
    );
});