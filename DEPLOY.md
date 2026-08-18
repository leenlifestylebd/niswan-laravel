# ডেপ্লয় গাইড — Hostinger Premium (শেয়ার্ড হোস্টিং)

অ্যাপটা **MySQL/MariaDB ও PostgreSQL দুটোতেই চলে**, তাই Hostinger-এর MySQL দিয়েই
সব কাজ করবে — বাইরের কোনো ডেটাবেজ সার্ভিস লাগবে না।

আপনার WordPress সাইট আগের মতোই চলবে; এটা একটা **আলাদা সাবডোমেইনে** বসবে
(যেমন `shop.leen.com.bd`)।

> **কিউ ওয়ার্কার বা ক্রন জব লাগে না** — অ্যাপে কোনো queued job বা scheduled task নেই।

---

## ০. যা লাগবে

| দরকার | Hostinger-এ কোথায় |
|---|---|
| PHP **8.2+** | hPanel → Advanced → **PHP Configuration** |
| এক্সটেনশন: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `zip`, `gd`, `intl` | একই পেজের **PHP extensions** ট্যাব |
| MySQL ডেটাবেজ | hPanel → Databases → **Management** |
| SSH অ্যাক্সেস | hPanel → Advanced → **SSH Access** (Premium প্ল্যানে আছে) |

`intl` না থাকলেও চলবে — টাকার অঙ্ক তখন ফলব্যাক দিয়ে বাংলা সংখ্যায় দেখাবে।
বাকিগুলো অবশ্যই লাগবে।

---

## ১. ডেটাবেজ বানান

hPanel → **Databases → Management** → নতুন ডেটাবেজ:

- Database name: `u123456_niswan` (Hostinger নিজেই prefix বসাবে)
- Username, password — **তিনটাই কপি করে রাখুন**, `.env` এ লাগবে

Character set যেন **utf8mb4** হয় (বাংলা লেখার জন্য) — Hostinger-এ এটাই ডিফল্ট।

---

## ২. সাবডোমেইন বানান

hPanel → Domains → **Subdomains** → `shop` যোগ করুন।

Hostinger এটার জন্য ফোল্ডার বানাবে: `~/domains/shop.leen.com.bd/public_html`

---

## ৩. কোড আপলোড (SSH)

hPanel → Advanced → **SSH Access** থেকে হোস্ট, পোর্ট ও ইউজারনেম নিন, তারপর:

```bash
ssh -p <port> u123456@<host>
```

```bash
cd ~/domains/shop.leen.com.bd && git clone https://github.com/leenlifestylebd/niswan-laravel.git app
```

এখন গঠনটা এমন:

```
~/domains/shop.leen.com.bd/
├── app/            ← Laravel কোড (ওয়েব থেকে দেখা যাবে না)
└── public_html/    ← এটা app/public এর দিকে দেখাবে (পরের ধাপ)
```

### public_html কে app/public এর দিকে পাঠান

```bash
cd ~/domains/shop.leen.com.bd && rm -rf public_html && ln -s app/public public_html
```

> `public_html` মুছে ফেলার আগে দেখে নিন ভেতরে দরকারি কিছু নেই (নতুন সাবডোমেইনে
> শুধু Hostinger-এর ডিফল্ট পেজ থাকে)।

symlink কাজ না করলে বিকল্প: `app/public` এর সব ফাইল `public_html` এ কপি করে
`public_html/index.php` এর দুটো পাথ `__DIR__.'/../app/...'` করে দিন।

---

## ৪. নির্ভরতা ইনস্টল

```bash
cd ~/domains/shop.leen.com.bd/app && composer install --no-dev --optimize-autoloader
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
`~/domains/shop.leen.com.bd/app/public/build/` এ আপলোড করুন
(hPanel → File Manager, অথবা FileZilla/SFTP)।

> ⚠️ `public/build` গিটে যায় না — **ডিজাইন বা CSS বদলালে প্রতিবার আবার আপলোড করতে হবে**,
> নাহলে সাইট স্টাইল ছাড়া দেখাবে।

---

## ৬. `.env` তৈরি

```bash
cd ~/domains/shop.leen.com.bd/app && cp .env.example .env && php artisan key:generate
```

`nano .env` দিয়ে এডিট করুন:

```
APP_NAME=NISWAN
APP_ENV=production
APP_DEBUG=false
APP_URL=https://shop.leen.com.bd

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456_niswan
DB_USERNAME=u123456_niswan
DB_PASSWORD=<আপনার পাসওয়ার্ড>

ADMIN_PASSWORD=<প্রথম লগইনের পাসওয়ার্ড>
UPLOAD_DIR=/home/u123456/domains/shop.leen.com.bd/app/storage/uploads
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

## ৯. HTTPS

hPanel → Security → **SSL** → সাবডোমেইনের জন্য Let's Encrypt চালু করুন,
তারপর **Force HTTPS** অন করুন।

---

## ১০. ডেপ্লয়ের পরে — চেকলিস্ট

1. `https://shop.leen.com.bd/admin` এ `.env` এর `ADMIN_PASSWORD` দিয়ে লগইন
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
cd ~/domains/shop.leen.com.bd/app && git pull && composer install --no-dev -o && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
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

---

## বিকল্প: VPS + Coolify

VPS-এ দিতে চাইলে রিপোতে `Dockerfile` ও `docker/` (nginx + php-fpm + supervisor)
রেডি আছে। সংক্ষেপে: Coolify-তে Dockerfile অ্যাপ (port 80), আলাদা Postgres বা MySQL
সার্ভিস, persistent volume `/app/storage`, Domains-এ apex ও www দুটোই, আর
Cloudflare DNS-এ `@` ও `www` **Proxied CNAME →
`35ac07ef-c56d-4b80-bec7-26db28161178.cfargotunnel.com`**।

> ⚠️ VPS-এর মুম্বাই IP বাংলাদেশ থেকে blackholed — সরাসরি A-রেকর্ড নয়, অবশ্যই
> Cloudflare Tunnel দিয়ে সার্ভ করতে হবে।
