# NISWAN — Laravel Store

`C:\Users\User\ihda-shop` এর Next.js e-commerce টেমপ্লেটের **Laravel পোর্ট** (full feature parity)।
স্ট্যাক: **Laravel 12 + Blade + Tailwind CSS 4 + Alpine.js**, DB **PostgreSQL**।

> ℹ️ স্পেকে Laravel 11 বলা ছিল, কিন্তু Laravel 11-এর সব রিলিজ এখন unpatched security advisory-তে
> ব্লকড (composer ইনস্টল করতেই দেয় না)। তাই **Laravel 12** — একই Blade/Tailwind/Alpine স্ট্যাক,
> PHP 8.2+ সাপোর্ট, সক্রিয়ভাবে প্যাচ হয়।

---

## ফিচার

**পাবলিক সাইট — সিঙ্গেল পেজ, এক প্রোডাক্ট**

পুরো স্টোরফ্রন্ট একটাই পেজ (`/`), একটাই প্রোডাক্ট। ডিজাইন **ডার্ক লাক্সারি** —
গাঢ় ব্যাকগ্রাউন্ড, সোনালি অ্যাকসেন্ট, ধারালো কোণা, ডিসপ্লে সেরিফ **Tiro Bangla** +
বডি **Anek Bangla**।

সেকশনের ক্রম:
1. **ব্যানার হিরো** — ফুল-ব্লিড ব্যানার (অ্যাডমিন থেকে আপলোড, ডেস্কটপ/মোবাইল আলাদা),
   সোনালি ফ্রেম, হেডলাইন, দাম ও CTA
2. **ঘোষণা স্ট্রিপ** — একটানা চলমান মার্কি
3. **শোকেস** — থাম্বনেইল + বড় ছবি, ফিচার লিস্ট, সাইজ
4. **প্রতিশ্রুতি** — নাম্বারড কলাম (০১–০৪)
5. **গ্যালারি** — অসম উচ্চতার এডিটোরিয়াল গ্রিড
6. **গ্রাহকের কথা** — রিভিউ স্ক্রিনশট স্লাইডার
7. **অর্ডার ফর্ম** — ভ্যারিয়েন্ট/সাইজ/পরিমাণ/এলাকা, ডেলিভারি চার্জ হিসাব,
   **ফোন ১১-ডিজিট যাচাই** (`^01\d{9}$`, `+880`/`880` প্রিফিক্স গ্রহণযোগ্য) —
   client ও server দুই জায়গায়
8. **ফুটার** + মোবাইলে নিচে **স্থায়ী অর্ডার বার** (WhatsApp + দাম সহ CTA)

- কোন প্রোডাক্টটি দেখাবে তা অ্যাডমিন → সেটিংস থেকে বাছাই; খালি রাখলে প্রথম সক্রিয়
  প্রোডাক্ট। বাকি প্রোডাক্টগুলো অ্যাডমিনে থাকে, সাইটে দেখায় না।
- পুরোনো `/product/{slug}` লিংক ৩০১ রিডাইরেক্টে হোমে যায় — SEO ভাঙে না।
- SEO: JSON-LD (Product), OpenGraph, canonical

**অ্যাডমিন প্যানেল** (`/admin`)
- ড্যাশবোর্ড: অর্ডার লিস্ট + স্ট্যাটাস স্ট্যাট
- অর্ডার: স্ট্যাটাস বদল, **পূর্ণ এডিট** (product/size/qty/area/price/address) — এলাকা বদলালে
  ডেলিভারি চার্জ ও সর্বমোট অটো
- Steadfast এ পাঠানো (single + bulk) + **লেবেল প্রিন্ট (৫০×৭৫mm)**
- প্রোডাক্ট CRUD + ইমেজ আপলোড + **অটো unique slug** (বাংলা/ল্যাটিন, `-2`/`-3`, খালি হলে `product`)
- রিভিউ ম্যানেজমেন্ট
- অ্যানালিটিক্স: ভিজিটর + বিক্রি মেট্রিক, **custom date range** (আজ/৭/৩০/৯০/১বছর + from–to)
- সেটিংস: ব্র্যান্ড, **ল্যান্ডিং প্রোডাক্ট + ব্যানার আপলোড (ডেস্কটপ/মোবাইল) + হিরো টেক্সট**,
  রঙ (অ্যাকসেন্ট/ব্যাকগ্রাউন্ড), ডেলিভারি চার্জ, **এনক্রিপ্টেড সিক্রেট টোকেন**
- অ্যাডমিন পাসওয়ার্ড বদল

অ্যাডমিন প্যানেলও স্টোরফ্রন্টের মতো **ডার্ক লাক্সারি** — বাঁয়ে স্থায়ী **সাইডবার**
(মোবাইলে ড্রয়ার), একই রঙ ও ফন্ট, ধারালো কোণা। পেজগুলো সার্ভার-রেন্ডারড থাকে
(SPA নয়) — ফর্ম সাবমিট ও রিডাইরেক্ট ফ্লো নির্ভরযোগ্য রাখতে।

**ইন্টিগ্রেশন**
- Telegram — নতুন অর্ডারে নোটিফিকেশন
- Steadfast Courier API — এক ক্লিকে কনসাইনমেন্ট
- Meta Pixel (browser) + **Conversions API (server)**, একই `event_id` দিয়ে dedup
- Built-in analytics — `sendBeacon` → visit log (PII নেই, visitor একমুখী হ্যাশ)
- PWA — customer (`/manifest.webmanifest`) ও admin (`/admin/manifest.webmanifest`) আলাদা

---

## লোকাল সেটআপ

দরকার: PHP 8.2+ (`pdo_pgsql`, `intl`, `openssl`, `zip`, `gd`), Composer, Node 20+, PostgreSQL।

```bash
composer install
```

```bash
npm install
```

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

`.env` এ DB বসাও:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=niswan_laravel
DB_USERNAME=...
DB_PASSWORD=...
ADMIN_PASSWORD=admin123   # প্রথম লগইনের জন্য; পরে অ্যাডমিন → সেটিংস থেকে বদলাও
```

মাইগ্রেশন + ডেমো ডেটা, তারপর অ্যাসেট বিল্ড ও সার্ভার:

```bash
php artisan migrate --seed
```

```bash
npm run build
```

```bash
php artisan serve --port=8000
```

- সাইট: http://localhost:8000
- অ্যাডমিন: http://localhost:8000/admin  (পাসওয়ার্ড = `.env` এর `ADMIN_PASSWORD`)

ডেভেলপমেন্টে হট-রিলোড চাইলে আলাদা টার্মিনালে `npm run dev`।

---

## গুরুত্বপূর্ণ: APP_KEY

Steadfast / Telegram / Meta CAPI টোকেন DB-তে **`Crypt::encryptString`** দিয়ে এনক্রিপ্টেড থাকে।

> ⚠️ **`APP_KEY` হারালে বা বদলালে টোকেনগুলো আর ডিক্রিপ্ট হবে না** — অ্যাডমিন সেটিংস থেকে আবার
> বসাতে হবে। প্রতি ক্লায়েন্টের জন্য আলাদা `APP_KEY` রাখো এবং নিরাপদে সেভ করো।

---

## ডেপ্লয় (Coolify + Cloudflare Tunnel)

1. Coolify-তে নতুন **Dockerfile** অ্যাপ — এই রিপো, port **80** expose।
2. আলাদা **Postgres** সার্ভিস → env এ `DB_*` বসাও।
3. env: `APP_KEY` (`php artisan key:generate --show`), `APP_ENV=production`, `APP_DEBUG=false`,
   `APP_URL=https://<domain>`, `DB_*`, `ADMIN_PASSWORD` (প্রথম লগইন)।
4. **Persistent volume** → `/app/storage` (আপলোড করা ছবি এখানে; না দিলে redeploy-এ মুছে যাবে)।
5. Domains ফিল্ডে **apex + www দুইটাই**: `https://domain,https://www.domain`
6. Cloudflare DNS: `@` ও `www` → **Proxied CNAME →
   `35ac07ef-c56d-4b80-bec7-26db28161178.cfargotunnel.com`**
   (একই tunnel, ingress catch-all — cloudflared-এ কিছু বদলাতে হবে না)।
7. ডেপ্লয়ের পর অ্যাডমিন → সেটিংস থেকে ব্র্যান্ড, রঙ, ডেলিভারি চার্জ ও টোকেনগুলো বসাও।

> ⚠️ VPS-এর মুম্বাই IP বাংলাদেশ থেকে blackholed — তাই **অবশ্যই Cloudflare Tunnel দিয়েই** সার্ভ
> করতে হবে, সরাসরি A-রেকর্ড নয়। ডেপ্লয়ের পর **Robi/বাংলাদেশি নেটওয়ার্কে টেস্ট করো**।

`docker/entrypoint.sh` কনটেইনার চালুর সময় `migrate --force`, `db:seed --force` ও
`config/route/view:cache` চালায়।

---

## গঠন

```
app/
  Http/Controllers/          পাবলিক: Home, Product, Order, Track, Media, Manifest
  Http/Controllers/Admin/    Auth, Order, Product, Review, Settings, Analytics
  Http/Middleware/AdminAuth  /admin/* সুরক্ষা (সেশন-ভিত্তিক)
  Models/                    Product, Order, Setting, Review, Visit
  Services/
    SettingsService          key/value সেটিংস + Crypt এনক্রিপশন
    AdminAuthService         পাসওয়ার্ড হ্যাশ/যাচাই (bcrypt, settings টেবিলে)
    ProductService           slugify + unique slug + ফর্ম ইনপুট clean
    UploadService            লোকাল ডিস্কে ছবি সেভ (persistent volume)
    AnalyticsService         visit log + visitor/order stats (day/week/month bucket)
    TelegramService          অর্ডার নোটিফিকেশন
    SteadfastService         কুরিয়ার কনসাইনমেন্ট
    FbCapiService            Meta Conversions API (event_id dedup)
  Support/                   Phone (BD নাম্বার), helpers (bdt/bn_num/lighten)
resources/views/
  layouts/{app,admin}        পাবলিক (ডার্ক) ও অ্যাডমিন (হালকা) লেআউট — রঙ CSS variable
  landing.blade.php          সিঙ্গেল পেজ — সব সেকশন এখানে include হয়
  partials/                  topbar, banner-hero, marquee-strip, showcase, assurance,
                             gallery, voices, order, site-footer, sticky-bar
  admin/                     orders, products/, reviews, settings, analytics, labels, login
  admin/partials/sidebar     অ্যাডমিন সাইডবার (ডেস্কটপে স্থায়ী, মোবাইলে ড্রয়ার)
routes/{web,admin}.php
docker/                      nginx.conf, supervisord.conf, php.ini, entrypoint.sh
```

---

## ডেমো ডেটা মুছতে

```bash
php artisan tinker --execute="App\Models\Visit::truncate(); App\Models\Order::truncate();"
```
