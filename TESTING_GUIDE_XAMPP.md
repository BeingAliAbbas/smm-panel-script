# Quick Testing Guide for XAMPP/Localhost

## ⚠️ IMPORTANT: Updated Fix (v1.0.1)

**If you're still seeing "ERR_INTERNET_DISCONNECTED" after following the steps, the service worker has been updated to fix path issues.**

### What Changed:
- Service worker now automatically detects the correct base path
- Works whether your app is at `http://localhost/` or `http://localhost/myproject/`
- Better error logging to help diagnose issues

### Quick Diagnostic:
After visiting your site, open DevTools Console and check for:
- ✓ "Service Worker registered successfully" ← Good!
- ✓ "Base path detected: /your-path/" ← Good!
- ✓ "Cached: /your-path/offline.html" ← Good!
- ✗ "Failed to cache" errors ← Problem!

If you see cache failures, the offline page won't work. See troubleshooting below.

---

## How to Test Offline Fallback on XAMPP

### ⚠️ Common Mistake

**DON'T:** Stop Apache and expect the offline page to show
**WHY:** Service workers can't intercept requests when the server never responds

### ✅ Correct Testing Method

Follow these steps in order:

---

## Step 1: Register the Service Worker

```
1. Start XAMPP Control Panel
2. Click "Start" on Apache
3. Open Chrome/Edge browser
4. Visit: http://localhost/your-project-folder
5. Press F12 to open DevTools
6. Check Console tab for:
   "Service Worker registered successfully"
```

**Screenshot Location:** Console tab showing success message

---

## Step 2: Enable Offline Simulation

**Option A: Service Worker Method (Recommended)**

```
1. With page loaded and Apache running
2. In DevTools, go to: Application tab
3. Click "Service Workers" in left sidebar
4. Check the "Offline" checkbox
5. Refresh the page (F5)
```

**What happens:** Custom offline page appears!

**Option B: Network Throttling Method**

```
1. With page loaded and Apache running
2. In DevTools, go to: Network tab
3. Find dropdown that says "No throttling"
4. Change it to "Offline"
5. Refresh the page (F5)
```

---

## Step 3: Test Recovery

```
1. On the offline page, click "Retry Connection"
2. Or uncheck "Offline" in DevTools
3. Page automatically reloads
4. Site loads normally
```

---

## Visual Guide

### Where to Find Service Worker Settings

```
DevTools (F12)
  └── Application Tab (top tabs)
       └── Service Workers (left sidebar)
            └── [✓] Offline checkbox
```

### Where to Find Network Throttling

```
DevTools (F12)
  └── Network Tab (top tabs)
       └── [No throttling ▼] dropdown (top toolbar)
            └── Select "Offline"
```

---

## What You Should See

### When Online (Apache Running)
- Normal website loads
- Console shows: "Service Worker registered successfully"

### When Offline Simulation Active
- Custom purple gradient page appears
- Message: "No Internet Connection"
- "Retry Connection" button visible
- Connection status indicator shows "No internet connection"

### When Back Online
- Click retry button
- Status changes to "Connection restored! Reloading..."
- Page automatically reloads to normal site

---

## Troubleshooting

### "ERR_CONNECTION_REFUSED" appears instead of offline page

**Cause:** Apache is completely stopped OR service worker was never registered

**Solution:**
1. Start Apache in XAMPP
2. Visit the site once to register service worker
3. Then use DevTools offline mode (don't stop Apache)

### Service worker not registering

**Symptoms:** No console message about service worker

**Solutions:**
1. Check that `service-worker.js` is in root directory
2. Clear browser cache (Ctrl+Shift+Delete)
3. Hard refresh (Ctrl+F5)
4. Check browser console for errors

### Offline page doesn't appear (shows ERR_INTERNET_DISCONNECTED)

**Symptoms:** Browser's default offline error appears instead of custom page

**Root Causes:**
1. Service worker never cached the offline page successfully
2. Service worker registered but caching failed
3. Path mismatch (common in subdirectory installations)

**Diagnostic Steps:**
```javascript
// Paste in Console to check what's cached
caches.keys().then(keys => {
  console.log('Cache keys:', keys);
  keys.forEach(k => caches.open(k).then(c => c.keys().then(r => 
    console.log(k + ':', r.map(req => req.url))
  )));
});
```

**Solutions:**
1. **Clear everything and start fresh:**
   - DevTools > Application > Storage > Clear site data
   - Close and reopen browser
   - Visit site again with Apache running
   - Watch Console for cache success messages

2. **Check console for caching errors:**
   - Look for "Failed to cache" messages
   - If you see 404 errors, files aren't in the right location
   - Service worker auto-detects base path now (v1.0.1+)

3. **Verify service worker is active:**
   ```javascript
   navigator.serviceWorker.getRegistration().then(reg => {
     console.log('State:', reg?.active?.state);
   });
   ```
   Should show "activated"

4. **Force re-register:**
   - DevTools > Application > Service Workers
   - Click "Unregister"
   - Refresh page to re-register
   - Wait for caching to complete

### Still not working?

**Check your project structure:**
```
http://localhost/myproject/
  ├── service-worker.js  ← Must be here
  ├── offline.html       ← Must be here  
  ├── assets/
  ├── themes/
  └── ...
```

**Console should show:**
```
✓ Service Worker registered successfully
  Scope: http://localhost/myproject/
  Active: Yes
[Service Worker] Base path detected: /myproject/
[Service Worker] Cached: /myproject/offline.html
[Service Worker] Cached: /myproject/assets/css/core.css
```

If you don't see "Cached" messages, the service worker couldn't find the files.

---

## Important Notes

✅ **Server MUST be running** when you test offline mode
✅ **Use DevTools** to simulate offline, not stopping Apache
✅ **Visit site first** before testing offline functionality
✅ **Keep browser tab open** during testing

❌ **Don't stop Apache** to test offline mode
❌ **Don't expect it to work on first visit** before service worker registers
❌ **Don't clear browser data** between tests (it removes service worker)

---

## Real-World Scenarios

The offline page will work in these situations:

1. **User visits your site** → Service worker registers
2. **User's WiFi drops** → Offline page appears
3. **User turns on airplane mode** after visiting → Offline page appears
4. **User's internet disconnects** temporarily → Offline page appears

The offline page will NOT work in:

1. **Server is down** → Cannot register service worker in the first place
2. **First-time visitor with no internet** → Service worker never registered
3. **Apache not started** → Browser can't reach localhost at all

---

## Quick Commands

**Check if service worker is registered (paste in Console):**
```javascript
navigator.serviceWorker.getRegistrations().then(r => console.log('Service Workers:', r));
```

**Check cached files (paste in Console):**
```javascript
caches.keys().then(keys => keys.forEach(k => caches.open(k).then(c => c.keys().then(reqs => console.log(k, reqs.map(r => r.url))))));
```

**Unregister service worker (if needed):**
```javascript
navigator.serviceWorker.getRegistrations().then(r => r.forEach(sw => sw.unregister()));
```

---

## Summary

1. **Start Apache** ✓
2. **Visit site** ✓
3. **Open DevTools** ✓
4. **Enable Offline mode in DevTools** ✓
5. **Refresh page** ✓
6. **See custom offline page** ✓

That's it! The server stays running the whole time - you're just simulating offline mode in the browser.
