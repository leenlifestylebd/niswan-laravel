# IHDA Store — Laravel Build Spec (handoff)

এই ডকুমেন্ট একটা বিদ্যমান **Next.js e-commerce store template**-এর হুবহু Laravel পোর্ট বানানোর জন্য। মূল প্রজেক্ট: `C:\Users\User\ihda-shop` (Next.js 16 + React 19 + Postgres)। **এই Laravel প্রজেক্ট সম্পূর্ণ আলাদা — ওই Next.js কোডে হাত দেবে না।**

> স্ট্যাক সিদ্ধান্ত: **Laravel 11 + Blade + Tailwind CSS + Alpine.js** (server-rendered, অল্প interactivity Alpine দিয়ে)। DB: **PostgreSQL**। লক্ষ্য: **full feature parity**।

---

## 0. Conflict-free setup

- ফোল্ডার: `C:\Users\User\niswan-laravel` (এই ফোল্ডার)
- আলাদা Git repo (যেমন `niswan-laravel`), Next.js repo-র সাথে মিশাবে না
- আলাদা Postgres database (একই সার্ভার হলেও ভিন্ন DB নাম)
- Local port: `8000` (`php artisan serve`)
- VPS-এ আলাদা Coolify app (PHP build), একই Cloudflare Tunnel-এ যুক্ত হবে

---

## 1. যা যা পোর্ট হবে (parity checklist)

**পাবলিক সাইট**
- [ ] হোমপেজ: Hero + প্রোডাক্ট গ্রিড (DB থেকে, active + sort_order অনুযায়ী)
- [ ] প্রোডাক্ট পেজ `/product/{slug}`: গ্যালারি, ভ্যারিয়েন্ট, সাইজ, অর্ডার ফর্ম
- [ ] অর্ডার ফর্ম: ভ্যারিয়েন্ট/সাইজ/পরিমাণ/এলাকা, ডেলিভারি চার্জ হিসাব, **ফোন ১১-ডিজিট যাচাই** (`^01\d{9}$`, +880/880 মানে)
- [ ] WhatsApp-এ অর্ডার বাটন
- [ ] রিভিউ/ফিডব্যাক গ্যালারি (স্ক্রিনশট)

**অ্যাডমিন প্যানেল** (`/admin`, পাসওয়ার্ড-সুরক্ষিত)
- [ ] ড্যাশবোর্ড: অর্ডার লিস্ট, স্ট্যাটাস স্ট্যাট
- [ ] অর্ডার: স্ট্যাটাস বদল, **অর্ডার এডিট** (product/size/qty/area/price/address; এলাকা বদলালে ডেলিভারি চার্জ অটো)
- [ ] Steadfast এ পাঠানো (single + bulk) + লেবেল প্রিন্ট (৫০×৭৫mm)
- [ ] প্রোডাক্ট CRUD + ইমেজ আপলোড + **অটো unique slug** (নাম থেকে; `-2/-3` suffix; খালি হলে `product`)
- [ ] রিভিউ ম্যানেজমেন্ট (DB)
- [ ] অ্যানালিটিক্স: ভিজিটর + বিক্রি মেট্রিক, **custom date range** (আজ/৭/৩০/৯০/১বছর + from–to)
- [ ] সেটিংস: ব্র্যান্ড, রঙ, ডেলিভারি চার্জ, সিক্রেট টোকেন (এনক্রিপ্টেড)
- [ ] অ্যাডমিন পাসওয়ার্ড বদল

**ইন্টিগ্রেশন**
- [ ] Telegram — নতুন অর্ডারে নোটিফিকেশন
- [ ] Steadfast Courier API — এক ক্লিকে কনসাইনমেন্ট
- [ ] Meta Pixel (browser) + **Conversions API (server)**, একই `event_id` দিয়ে dedup
- [ ] Built-in analytics — sendBeacon → visit log (privacy-friendly, PII নেই)
- [ ] PWA — customer + admin আলাদা manifest

---

## 2. Database schema (PostgreSQL migrations)

Next.js যে টেবিল ব্যবহার করে — হুবহু একই রাখো (একই কলাম নাম, snake_case)।

**products**
```
id BIGSERIAL PK
slug TEXT UNIQUE NOT NULL
name TEXT NOT NULL
price INTEGER NOT NULL
old_price INTEGER NULL
badge TEXT NULL
short TEXT NULL
image TEXT NULL
images JSONB DEFAULT '[]'
sizes JSONB DEFAULT '[]'
variants JSONB DEFAULT '[]'      -- [{name, price}]
features JSONB DEFAULT '[]'
sort_order INTEGER DEFAULT 0
active BOOLEAN DEFAULT true
created_at TIMESTAMPTZ DEFAULT now()
```

**orders**
```
id BIGSERIAL PK
created_at TIMESTAMPTZ DEFAULT now()
name TEXT NOT NULL
phone TEXT NOT NULL
address TEXT NOT NULL
product TEXT NULL
slug TEXT NULL
size TEXT NULL
color TEXT NULL
qty INTEGER DEFAULT 1
area TEXT NULL
delivery_charge INTEGER DEFAULT 0
total INTEGER DEFAULT 0
status TEXT DEFAULT 'pending'   -- pending|confirmed|sent_to_courier|delivered|cancelled
consignment_id TEXT NULL
tracking_code TEXT NULL
```

**settings** (key/value; সিক্রেটগুলো এনক্রিপ্টেড, `enc:` prefix)
```
key TEXT PRIMARY KEY
value TEXT
```

**reviews**
```
id BIGSERIAL PK
created_at TIMESTAMPTZ DEFAULT now()
image TEXT
name TEXT NULL
active BOOLEAN DEFAULT true
sort_order INTEGER DEFAULT 0
```
(বর্তমান কোড থেকে exact কলাম মিলিয়ে নিও — `C:\Users\User\ihda-shop\lib\reviews-db.js`)

**visits** (analytics)
```
id BIGSERIAL PK
created_at TIMESTAMPTZ DEFAULT now()
path TEXT
ref TEXT       -- referrer host: facebook/google/direct
visitor TEXT   -- sha256(ip|ua|APP_KEY).slice(0,24) — irreversible, PII নেই
device TEXT    -- mobile|desktop
INDEX (created_at)
```

Laravel Eloquent model: Product, Order, Setting, Review, Visit। JSONB কলামে `$casts = ['images'=>'array', ...]`।

---

## 3. Routes / Controllers

**Public**
- `GET /` → HomeController@index (active products)
- `GET /product/{slug}` → ProductController@show
- `POST /order` → OrderController@store (validate phone, save, Telegram, CAPI)
- `POST /track` → TrackController@store (beacon; /admin skip)

**Admin** (middleware: `admin.auth`)
- `GET /admin/login`, `POST /admin/login`, `POST /admin/logout`
- `GET /admin` → orders dashboard
- `GET /admin/products`, `/products/create`, `/products/{id}/edit` + POST/PUT/DELETE
- `GET /admin/reviews` + CRUD
- `GET /admin/analytics` (query: range / from,to)
- `GET /admin/settings` + POST
- `POST /admin/orders/{id}` → order edit (product/size/qty/area/charge/total; phone validated)
- `POST /admin/status` → status change
- `POST /admin/steadfast`, `/steadfast/bulk`
- `GET /admin/labels?ids=`
- `POST /admin/password`
- `POST /admin/upload` → ইমেজ আপলোড (লোকাল ডিস্ক)

---

## 4. মূল লজিক (Next.js থেকে হুবহু নিয়ম)

**Slug (products)** — নাম → slugify: `strtolower`, latin+bengali রাখো (`[^a-z0-9\x{0980}-\x{09FF}]+` → `-`), unique নিশ্চিত করো (`-2/-3`), খালি হলে `product`।

**ফোন যাচাই** — digits বের করো, `^88` থাকলে বাদ, তারপর `^01\d{9}$` না মিললে reject (client + server দুই জায়গায়)।

**ডেলিভারি/টোটাল** — subtotal = variant.price × qty; total = subtotal + delivery_charge; এলাকা inside/outside অনুযায়ী চার্জ সেটিংস থেকে।

**সিক্রেট এনক্রিপশন** — Steadfast/Telegram/CAPI টোকেন DB-তে **Laravel `Crypt::encryptString`** দিয়ে এনক্রিপ্ট করে রাখো (Next.js AES-256-GCM `enc:` prefix করত — Laravel-এ নিজের `Crypt` ব্যবহার করো, format ভিন্ন হলেও সমস্যা নেই কারণ নতুন DB)। ⚠️ `APP_KEY` হারালে ডিক্রিপ্ট হবে না — প্রতি ক্লায়েন্টে আলাদা ও সেভ রাখতে হবে।

**অ্যাডমিন auth** — সহজ: সেটিংসে হ্যাশড পাসওয়ার্ড; login → session; middleware দিয়ে `/admin/*` সুরক্ষা (login বাদে)।

---

## 5. Integrations (endpoint/নিয়ম)

**Telegram** — `POST https://api.telegram.org/bot{TOKEN}/sendMessage` `{chat_id, text}`; অর্ডার সামারি ফরম্যাট।

**Steadfast** — base `https://portal.packzy.com/api/v1`; header `Api-Key`, `Secret-Key`; create order endpoint `/create_order` `{invoice, recipient_name, recipient_phone, recipient_address, cod_amount, note}`; response থেকে `consignment_id`, `tracking_code`।

**Meta Conversions API** — `POST https://graph.facebook.com/v21.0/{PIXEL_ID}/events?access_token=...`
- event: `Purchase`, `event_time`, `event_id` (browser fbq-র সাথে **একই**), `action_source: website`, `event_source_url`
- `user_data`: `ph` = sha256(880-normalized phone), `fn` = sha256(lowercase name), `client_ip_address`, `client_user_agent`, `fbp`, `fbc`
- `custom_data`: currency BDT, value total, content_name, content_ids [slug], content_type product, num_items qty
- `test_event_code` থাকলে যোগ (যাচাইয়ের সময়)
- browser: layout-এ fbq init + PageView; অর্ডার সফল হলে `fbq('track','Purchase', {...}, {eventID})`

**Image upload** — লোকাল ডিস্ক (Laravel `Storage` public disk বা `/app/storage`); সার্ভ URL `/api/media/{name}` বা `Storage::url`। VPS-এ persistent volume `/app/storage`।

**Analytics** — beacon `/track` → visit row; visitor hash = `substr(hash('sha256', ip.'|'.ua.'|'.env('APP_KEY')), 0, 24)`; getVisitorStats (live 5min, pv/uv, daily bucket day/week/month রেঞ্জ অনুযায়ী, sources, top pages, devices) + getOrderStats (revenue, orders, AOV, status, best sellers) — custom from/to।

---

## 6. Deployment (একই VPS, Cloudflare Tunnel)

- Coolify-তে নতুন **PHP/Laravel app** (Nixpacks বা Dockerfile — php-fpm + nginx)। Port সেট করো (nginx সাধারণত 80; Coolify Ports Exposes মিলিয়ে দাও)।
- আলাদা Postgres (Coolify) → `.env` DB_*।
- Domains ফিল্ডে **apex + www দুইটাই** (`https://domain,https://www.domain`)।
- Cloudflare DNS: `@` ও `www` → **Proxied CNAME → `35ac07ef-c56d-4b80-bec7-26db28161178.cfargotunnel.com`** (একই tunnel; ingress catch-all, তাই cloudflared-এ কিছু বদলাতে হবে না)।
- Persistent volume `/app/storage` (আপলোড)।
- `.env`: APP_KEY (`php artisan key:generate`), DB_*, তারপর অ্যাডমিন থেকে টোকেন বসাও।

> ⚠️ VPS-এর মুম্বাই IP বাংলাদেশ থেকে blackholed — তাই **অবশ্যই Cloudflare Tunnel দিয়েই** সার্ভ করতে হবে, সরাসরি A-রেকর্ড নয়।

---

## 7. রেফারেন্স — Next.js-এর কোন ফাইল কী করে

নতুন session চাইলে এগুলো পড়ে নিতে পারে (রেফারেন্স, কপি নয়):
- `lib/db.js` — orders schema + queries + getOrderStats + updateOrderFields
- `lib/products-db.js` — products + slugify/uniqueSlug
- `lib/settings.js`, `lib/crypto.js` — settings + এনক্রিপশন
- `lib/fb-capi.js`, `app/api/order/route.js` — CAPI + অর্ডার ফ্লো
- `lib/analytics-db.js`, `app/api/track/route.js` — analytics
- `lib/steadfast.js`, `lib/telegram.js` — কুরিয়ার/নোটিফিকেশন
- `components/OrderForm.jsx` — ফোন যাচাই, ডেলিভারি হিসাব, pixel
- `components/admin/*` — অ্যাডমিন UI (Orders/Products/Analytics/Settings)

---

## 8. Build order (সুপারিশ)

1. `composer create-project laravel/laravel .` → Tailwind + Alpine সেটআপ → pgsql `.env`
2. Migrations (products, orders, settings, reviews, visits) + Models + casts
3. Public: home + product + order (ফোন যাচাই + delivery হিসাব)
4. Admin auth + orders dashboard + status + **order edit**
5. Products CRUD + auto slug + image upload
6. Settings (এনক্রিপ্টেড টোকেন) + reviews
7. Integrations: Telegram → Steadfast → Meta CAPI (dedup)
8. Analytics (beacon + admin dashboard, custom range)
9. PWA (customer + admin manifest)
10. Dockerfile/Nixpacks → Coolify deploy → Cloudflare Tunnel DNS → Robi-তে টেস্ট
