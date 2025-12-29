// Service Worker for Offline Fallback Handling
// Version: 1.0.0

const CACHE_VERSION = 'smm-panel-v1.0.0';
const OFFLINE_PAGE = '/offline.html';
const OFFLINE_CACHE = 'offline-cache-v1';

// Assets to cache for offline use
const OFFLINE_ASSETS = [
  OFFLINE_PAGE,
  '/assets/css/core.css',
  '/assets/css/bootstrap/bootstrap.min.css',
  '/assets/js/vendors/jquery-3.2.1.min.js',
  '/assets/plugins/font-awesome/css/all.min.css'
];

// Install event - cache offline assets
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installing service worker...');
  
  event.waitUntil(
    caches.open(OFFLINE_CACHE)
      .then((cache) => {
        console.log('[Service Worker] Caching offline assets');
        return cache.addAll(OFFLINE_ASSETS.map(url => {
          // Ensure relative URLs work correctly
          return new Request(url, { cache: 'reload' });
        }));
      })
      .catch((error) => {
        console.error('[Service Worker] Failed to cache offline assets:', error);
      })
      .then(() => {
        console.log('[Service Worker] Installation complete');
        return self.skipWaiting(); // Activate immediately
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
              // Remove old caches
              return cacheName !== OFFLINE_CACHE && cacheName.startsWith('smm-panel-');
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
            return caches.match(OFFLINE_PAGE);
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
          
          caches.open(OFFLINE_CACHE)
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
              return cachedResponse;
            }
            
            // If it's a navigation request and not in cache, show offline page
            if (event.request.mode === 'navigate') {
              return caches.match(OFFLINE_PAGE);
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
