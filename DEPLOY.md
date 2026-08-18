# ডেপ্লয় গাইড — niswan.store · Hostinger Premium

অ্যাপটা **MySQL/MariaDB ও PostgreSQL দুটোতেই চলে**, তাই Hostinger-এর MySQL দিয়েই
সব কাজ করবে — বাইরের কোনো ডেটাবেজ সার্ভিস লাগবে না।

আপনার WordPress সাইট আগের মতোই চলবে; এই দোকানটা বসবে আলাদা ডোমেইন
**`niswan.store`** এ।

> ⚠️ **`niswan.store` এখন Cloudflare-এ আছে** (nameserver `otto/liz.ns.cloudflare.com`)
> আর proxy (কমলা মেঘ) চালু, কিন্তু পেছনে কোনো সার্ভার নেই — এখন খুললে
> **error 522** দেখায়। তাই DNS ও SSL-এর ধাপগুলো (ধাপ ২ ও ৯) **ক্রম মেনে**
> করতে হবে, নাহলে SSL ইস্যু হবে না বা রিডাইরেক্ট লুপে পড়বে।

> **কিউ ওয়ার্কার বা ক্রন জব লাগে না** — অ্যাপে কোনো queued job বা scheduled task নেই।

---

## ০. যা লাগবে

| দরকার | Hostinger-এ কোথায় |
|---|---|
| PHP **8.2+** (সুপারিশ **8.3 / 8.4**) | ওয়েবসাইটের Dashboard → Advanced → **PHP Configuration** |
| এক্সটেনশন: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `zip`, `gd`, `intl` | একই পেজের **PHP extensions** ট্যাব |
| MySQL ডেটাবেজ | hPanel → Databases → **Management** |
| SSH অ্যাক্সেস | hPanel → Advanced → **SSH Access** (Premium প্ল্যানে আছে) |

`intl` না থাকলেও চলবে — টাকার অঙ্ক তখন ফলব্যাক দিয়ে বাংলা সংখ্যায় দেখাবে।
বাকিগুলো অবশ্যই লাগবে।

**PHP ভার্সন সম্পর্কে:** `composer.json` এ শর্ত `^8.2`, মানে ৮.২ **বা তার নতুন**
যেকোনো ভার্সন — ৮.৩, ৮.৪, ৮.৫ সবই চলে (যাচাই করা: কোনো নির্ভরতা এদের আটকায় না)।
তবে অ্যাপটা পরীক্ষা করা হয়েছে **৮.২**-তে, তাই বহুল-ব্যবহৃত **৮.৩ বা ৮.৪** নেওয়াই
নিরাপদ। ভার্সন বদলানোর পর একবার চালাবেন:

```bash
composer install --no-dev -o && php artisan config:clear
```

---

## ১. ডেটাবেজ বানান

hPanel → **Databases → Management** → নতুন ডেটাবেজ:

- Database name: `u123456_niswan` (Hostinger নিজেই prefix বসাবে)
- Username, password — **তিনটাই কপি করে রাখুন**, `.env` এ লাগবে

Character set যেন **utf8mb4** হয় (বাংলা লেখার জন্য) — Hostinger-এ এটাই ডিফল্ট।

---

## ২. ডোমেইন যোগ করুন + Cloudflare DNS ঘোরান

### ২ক. Hostinger-এ ডোমেইন যোগ

hPanel → **Websites → Add Website** (বা Domains → **Add Domain**) → `niswan.store`

Hostinger ফোল্ডার বানাবে: `~/domains/niswan.store/public_html`
আর একটা **সার্ভার IP** দেখাবে — সেটা কপি করুন (hPanel → Websites → Dashboard →
**Server IP address**)।

### ২খ. Cloudflare-এ A রেকর্ড বদলান

Cloudflare ড্যাশবোর্ড → `niswan.store` → **DNS → Records**:

| Type | Name | Content | Proxy |
|---|---|---|---|
| A | `@` | `<Hostinger সার্ভার IP>` | **DNS only (ধূসর মেঘ)** — আপাতত |
| A | `www` | `<একই IP>` | **DNS only (ধূসর মেঘ)** — আপাতত |

> 🔸 **এখন অবশ্যই ধূসর মেঘ (DNS only) রাখুন।** কমলা মেঘ চালু থাকলে Let's Encrypt
> ডোমেইন যাচাই করতে পারে না, SSL ইস্যু হবে না। SSL হয়ে যাওয়ার পর ধাপ ৯-এ আবার
> কমলা করবেন।

Cloudflare-এ এখন একটা **redirect rule** আছে যেটা `niswan.store` → `www.niswan.store`
পাঠায়। কোনটা মূল ঠিকানা হবে ঠিক করে নিন (`www` ছাড়া রাখাই সহজ) — **Rules →
Redirect Rules** থেকে নিয়মটা বদলান বা মুছে দিন। যেটা বেছে নেবেন সেটাই `.env` এর
`APP_URL` এ বসবে।

DNS ছড়াতে ৫–৩০ মিনিট লাগতে পারে। যাচাই:

```bash
nslookup niswan.store 8.8.8.8
```

---

## ৩. কোড আপলোড (SSH)

hPanel → Advanced → **SSH Access** থেকে হোস্ট, পোর্ট ও ইউজারনেম নিন, তারপর:

```bash
ssh -p <port> u123456@<host>
```

```bash
cd ~/domains/niswan.store && git clone https://github.com/leenlifestylebd/niswan-laravel.git app
```

এখন গঠনটা এমন:

```
~/domains/niswan.store/
├── app/            ← Laravel কোড (ওয়েব থেকে দেখা যাবে না)
└── public_html/    ← এটা app/public এর দিকে দেখাবে (পরের ধাপ)
```

### public_html কে app/public এর দিকে পাঠান

```bash
cd ~/domains/niswan.store && rm -rf public_html && ln -s app/public public_html
```

> `public_html` মুছে ফেলার আগে দেখে নিন ভেতরে দরকারি কিছু নেই (নতুন ডোমেইনে
> শুধু Hostinger-এর ডিফল্ট পেজ থাকে)।

symlink কাজ না করলে বিকল্প: `app/public` এর সব ফাইল `public_html` এ কপি করে
`public_html/index.php` এর দুটো পাথ `__DIR__.'/../app/...'` করে দিন।

---

## ৪. নির্ভরতা ইনস্টল

```bash
cd ~/domains/niswan.store/app && composer install --no-dev --optimize-autoloader
```

Composer না পেলে:

```bash
curl -sS https://getcomposer.org/installer | php && php composer.phar install --no-dev --optimize-autoloader
```

---

## ৫. ফ্রন্টএন্ড অ্যাসেট (নিজের পিসিতে বিল্ড করে আপলোড)

Hostinger শেয়ার্ড হোস্টিংয়ে **Node.js নেই**, তাই CSS/JS নিজের কম্পিউটারে বিল্ড করতে হবে:

```bash
npm run build
```

তারপর তৈরি হওয়া `public/build/` ফোল্ডারটা সার্ভারের
`~/domains/niswan.store/app/public/build/` এ আপলোড করুন
(hPanel → File Manager, অথবা FileZilla/SFTP)।

> ⚠️ `public/build` গিটে যায় না — **ডিজাইন বা CSS বদলালে প্রতিবার আবার আপলোড করতে হবে**,
> নাহলে সাইট স্টাইল ছাড়া দেখাবে।

---

## ৬. `.env` তৈরি

```bash
cd ~/domains/niswan.store/app && cp .env.example .env && php artisan key:generate
```

`nano .env` দিয়ে এডিট করুন:

```
APP_NAME=NISWAN
APP_ENV=production
APP_DEBUG=false
APP_URL=https://niswan.store

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456_niswan
DB_USERNAME=u123456_niswan
DB_PASSWORD=<আপনার পাসওয়ার্ড>

ADMIN_PASSWORD=<প্রথম লগইনের পাসওয়ার্ড>
UPLOAD_DIR=/home/u123456/domains/niswan.store/app/storage/uploads
```

> 🔑 **`APP_KEY` আলাদা করে সেভ করে রাখুন।** Steadfast / Telegram / Meta-র টোকেন
> এই কী দিয়ে এনক্রিপ্ট হয়ে ডেটাবেজে থাকে — কী হারালে বা বদলালে টোকেনগুলো আর
> ডিক্রিপ্ট হবে না, অ্যাডমিন থেকে আবার বসাতে হবে।

---

## ৭. ডেটাবেজ ও পারমিশন

```bash
php artisan migrate --force --seed
```

```bash
mkdir -p storage/uploads && chmod -R 775 storage bootstrap/cache
```

---

## ৮. প্রোডাকশন ক্যাশ

```bash
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## ৯. HTTPS (ক্রম মেনে করুন)

**৯ক.** Cloudflare-এ দুটো A রেকর্ডই তখনো **DNS only (ধূসর মেঘ)** আছে কি না দেখে নিন।

**৯খ.** hPanel → Security → **SSL** → `niswan.store` এর জন্য Let's Encrypt ইনস্টল
করুন। ইস্যু হতে কয়েক মিনিট লাগে। হয়ে গেলে যাচাই করুন:

```bash
curl -sI https://niswan.store | head -3
```

**৯গ.** SSL ঠিকমতো কাজ করলে Cloudflare-এ ফিরে গিয়ে দুটো রেকর্ডই **Proxied
(কমলা মেঘ)** করে দিন।

**৯ঘ.** Cloudflare → **SSL/TLS → Overview** → মোড **Full (strict)** করুন।

> ⚠️ মোড **Flexible** রাখলে অসীম রিডাইরেক্ট লুপ হবে (Cloudflare HTTP-এ পাঠাবে,
> Hostinger আবার HTTPS-এ ঠেলবে)। Full (strict) ছাড়া অন্য কিছু দেবেন না।

**৯ঙ.** সবশেষে hPanel-এ **Force HTTPS** অন করুন।

---

## ১০. ডেপ্লয়ের পরে — চেকলিস্ট

1. `https://niswan.store/admin` এ `.env` এর `ADMIN_PASSWORD` দিয়ে লগইন
2. **সেটিংস → অ্যাডমিন পাসওয়ার্ড** — এখনই বদলান
3. **সেটিংস → ল্যান্ডিং পেজ** — ব্যানার আপলোড (ডেস্কটপ + মোবাইল আলাদা),
   হিরো হেডলাইন/সাবলাইন, কোন প্রোডাক্টটা দেখাবে
4. **সেটিংস → ব্র্যান্ড / যোগাযোগ** — নাম, ফোন, WhatsApp (কান্ট্রি কোড সহ, `+` ছাড়া:
   `8801XXXXXXXXX`), Facebook, Instagram
5. **সেটিংস → ডেলিভারি ও থিম** — ঢাকার ভিতরে/বাইরের চার্জ, রঙ
6. **সেটিংস → ইন্টিগ্রেশন** — Telegram Bot Token + Chat ID, Steadfast API Key +
   Secret, Meta Pixel ID + CAPI Token
7. **প্রোডাক্ট** — ডেমো ৫টা মুছে আসল প্রোডাক্ট দিন
8. **রিভিউ** — ডেমো মকআপ ৬টা মুছে **আসল গ্রাহকের স্ক্রিনশট** দিন
9. **একটা টেস্ট অর্ডার দিন** — Telegram-এ নোটিফিকেশন আসে কি না দেখুন, তারপর
   অ্যাডমিন থেকে সেই অর্ডারটা মুছে দিন
10. মোবাইল থেকে সাইট খুলে দেখুন — স্টিকি অর্ডার বার ও WhatsApp বাটন কাজ করে কি না

ডেমো ডেটা একসাথে মুছতে:

```bash
php artisan tinker --execute="App\Models\Visit::truncate(); App\Models\Order::truncate();"
```

---

## পরে আপডেট করতে

```bash
cd ~/domains/niswan.store/app && git pull && composer install --no-dev -o && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

ডিজাইন/CSS বদলালে লোকালি `npm run build` চালিয়ে `public/build/` আবার আপলোড করুন (ধাপ ৫)।

---

## সমস্যা হলে

| উপসর্গ | কারণ / সমাধান |
|---|---|
| সাদা পেজ বা 500 | `app/storage/logs/laravel.log` দেখুন। `storage` ও `bootstrap/cache` এ ৭৭৫ পারমিশন আছে কি না দেখুন |
| CSS/JS ছাড়া সাদামাটা পেজ | `public/build/` আপলোড হয়নি — ধাপ ৫ |
| `could not find driver` | hPanel → PHP Configuration এ **`pdo_mysql`** টিক দেওয়া নেই |
| `Table ... doesn't exist` | `php artisan migrate --force` চালানো হয়নি |
| সেটিংসের টোকেন হঠাৎ কাজ করছে না | `APP_KEY` বদলে গেছে — অ্যাডমিন থেকে টোকেনগুলো আবার বসান |
| আপলোড করা ছবি ৪০৪ | `UPLOAD_DIR` ভুল, বা `storage/uploads` এ লেখার পারমিশন নেই |
| ৪০৪ — index.php খুঁজে পাচ্ছে না | `public_html` symlink ঠিকমতো `app/public` এ যায়নি (ধাপ ৩) |
| পুরোনো কনফিগ ধরে আছে | `php artisan config:clear && php artisan config:cache` |
| বাংলা লেখা `???` দেখাচ্ছে | ডেটাবেজের charset **utf8mb4** নয় |
| **Cloudflare error 522** | A রেকর্ড Hostinger-এর IP তে যায়নি, অথবা ডোমেইনটা hPanel-এ যোগ করা হয়নি |
| **অসীম রিডাইরেক্ট (ERR_TOO_MANY_REDIRECTS)** | Cloudflare SSL/TLS মোড **Flexible** — **Full (strict)** করুন (ধাপ ৯ঘ) |
| SSL ইস্যু হচ্ছে না | Cloudflare proxy (কমলা মেঘ) চালু আছে — ধূসর করে আবার চেষ্টা করুন (ধাপ ৯ক) |
| `www` আর মূল ডোমেইন আলাদা আচরণ করছে | Cloudflare-এর পুরোনো redirect rule রয়ে গেছে (ধাপ ২খ) |

---

## বিকল্প: VPS + Coolify

VPS-এ দিতে চাইলে রিপোতে `Dockerfile` ও `docker/` (nginx + php-fpm + supervisor)
রেডি আছে। সংক্ষেপে: Coolify-তে Dockerfile অ্যাপ (port 80), আলাদা Postgres বা MySQL
সার্ভিস, persistent volume `/app/storage`, Domains-এ apex ও www দুটোই, আর
Cloudflare DNS-এ `@` ও `www` **Proxied CNAME →
`35ac07ef-c56d-4b80-bec7-26db28161178.cfargotunnel.com`**।

> ⚠️ VPS-এর মুম্বাই IP বাংলাদেশ থেকে blackholed — সরাসরি A-রেকর্ড নয়, অবশ্যই
> Cloudflare Tunnel দিয়ে সার্ভ করতে হবে।
