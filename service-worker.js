// Service Worker for Offline Fallback Handling
// Version: 1.0.1

const CACHE_NAME = 'smm-panel-offline-v1.1';
const CACHE_PREFIX = 'smm-panel-';

// Get the base path from the service worker's own location
// If SW is at /myproject/service-worker.js, base will be /myproject/
const swPath = self.location.pathname;
const BASE_PATH = swPath.substring(0, swPath.lastIndexOf('/') + 1);

const OFFLINE_PAGE = BASE_PATH + 'offline.html';

// Assets to cache for offline use (relative to base path)
const OFFLINE_ASSETS = [
  OFFLINE_PAGE,
  BASE_PATH + 'assets/css/core.css',
  BASE_PATH + 'assets/css/bootstrap/bootstrap.min.css',
  BASE_PATH + 'assets/js/vendors/jquery-3.2.1.min.js',
  BASE_PATH + 'assets/plugins/font-awesome/css/all.min.css'
];

// Install event - cache offline assets
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installing service worker...');
  console.log('[Service Worker] Base path detected:', BASE_PATH);
  console.log('[Service Worker] Offline page path:', OFFLINE_PAGE);
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[Service Worker] Caching offline assets');
        // Try to cache each asset individually to identify which ones fail
        return Promise.allSettled(
          OFFLINE_ASSETS.map(url => {
            return cache.add(new Request(url, { cache: 'no-cache' }))
              .then(() => {
                console.log('[Service Worker] Cached:', url);
              })
              .catch((error) => {
                console.warn('[Service Worker] Failed to cache:', url, error);
                // Don't fail the entire installation if one asset fails
              });
          })
        );
      })
      .then(() => {
        console.log('[Service Worker] Installation complete');
        return self.skipWaiting(); // Activate immediately
      })
      .catch((error) => {
        console.error('[Service Worker] Installation failed:', error);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activating service worker...');
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames
            .filter((cacheName) => {
              // Remove old caches that start with our prefix but aren't the current cache
              return cacheName !== CACHE_NAME && cacheName.startsWith(CACHE_PREFIX);
            })
            .map((cacheName) => {
              console.log('[Service Worker] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            })
        );
      })
      .then(() => {
        console.log('[Service Worker] Activation complete');
        return self.clients.claim(); // Take control immediately
      })
  );
});

// Fetch event - network first, fallback to offline page
self.addEventListener('fetch', (event) => {
  // Only handle GET requests
  if (event.request.method !== 'GET') {
    return;
  }

  // Skip cross-origin requests
  if (!event.request.url.startsWith(self.location.origin)) {
    return;
  }

  // Skip API calls and avoid caching dynamic content
  if (event.request.url.includes('/api/') || 
      event.request.url.includes('/ajax/') ||
      event.request.url.includes('?') ||
      event.request.url.includes('.php')) {
    
    // For navigation requests (page loads), provide offline fallback
    if (event.request.mode === 'navigate') {
      event.respondWith(
        fetch(event.request)
          .catch(() => {
            console.log('[Service Worker] Navigation failed, showing offline page');
            return caches.match(OFFLINE_PAGE).then(response => {
              if (response) {
                return response;
              }
              // If offline page not in cache, return a basic offline message
              return new Response(
                '<html><body><h1>Offline</h1><p>No internet connection. Please try again later.</p></body></html>',
                { headers: { 'Content-Type': 'text/html' } }
              );
            });
          })
      );
    }
    return;
  }

  // For static assets and pages
  event.respondWith(
    fetch(event.request)
      .then((response) => {
        // Clone the response before caching
        if (response.ok) {
          const responseToCache = response.clone();
          
          caches.open(CACHE_NAME)
            .then((cache) => {
              cache.put(event.request, responseToCache);
            });
        }
        
        return response;
      })
      .catch(() => {
        // If fetch fails, try to get from cache
        return caches.match(event.request)
          .then((cachedResponse) => {
            if (cachedResponse) {
              console.log('[Service Worker] Serving from cache:', event.request.url);
              return cachedResponse;
            }
            
            // If it's a navigation request and not in cache, show offline page
            if (event.request.mode === 'navigate') {
              console.log('[Service Worker] Navigation failed, showing offline page');
              return caches.match(OFFLINE_PAGE).then(response => {
                if (response) {
                  return response;
                }
                // Fallback offline message
                return new Response(
                  '<html><body><h1>Offline</h1><p>No internet connection. Please try again later.</p></body></html>',
                  { headers: { 'Content-Type': 'text/html' } }
                );
              });
            }
            
            // For other requests, return a basic error response
            return new Response('Network error', {
              status: 408,
              headers: { 'Content-Type': 'text/plain' }
            });
          });
      })
  );
});

// Message event - for communication with pages
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    event.waitUntil(
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => caches.delete(cacheName))
        );
      })
    );
  }
});
