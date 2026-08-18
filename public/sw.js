// খুব হালকা service worker — শুধু PWA installable রাখার জন্য।
// কোনো ক্যাশিং নেই, তাই অ্যাডমিন কিছু বদলালে সাথে সাথেই দেখা যায়।
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (e) => e.waitUntil(self.clients.claim()));
