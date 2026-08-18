# ডেপ্লয় গাইড — NISWAN Store

দুটো পথ দেওয়া আছে। আপনার হোস্টিং যেটা, সেই অংশে যান:

- **পথ ক** — cPanel / DirectAdmin শেয়ার্ড হোস্টিং (যেমন `app.leen.com.bd` যেভাবে চলছে)
- **পথ খ** — VPS + Coolify + Cloudflare Tunnel (Dockerfile দিয়ে)

---

## ০. আগে যা নিশ্চিত করতেই হবে

| দরকার | কেন |
|---|---|
| **PHP 8.2 বা তার উপরে** | Laravel 12-এর ন্যূনতম |
| **PostgreSQL** | ⚠️ নিচের নোট দেখুন — MySQL দিয়ে চলবে না |
| PHP এক্সটেনশন: `pdo_pgsql`, `intl`, `openssl`, `mbstring`, `fileinfo`, `curl`, `zip`, `gd` | DB, বাংলা সংখ্যা, টোকেন এনক্রিপশন, ছবি |
| আউটবাউন্ড HTTPS খোলা | Telegram / Steadfast / Meta CAPI কল করে |

> ### ⚠️ MySQL দিয়ে এই অ্যাপ চলবে না
>
> শুধু কনফিগ বদলালেই হবে না — কোডে PostgreSQL-নির্দিষ্ট জিনিস আছে:
> - `products` টেবিলে **`jsonb`** কলাম (images, sizes, variants, features)
> - অ্যানালিটিক্সে **`date_trunc()`**, **`count(*) FILTER (WHERE …)`**, **`::int`**,
>   **`interval '5 minutes'`** — সব Postgres সিনট্যাক্স
>
> আপনার হোস্টিংয়ে Postgres না থাকলে দুটো উপায়: হোস্টিং থেকে Postgres চালু করান,
> অথবা বাইরের একটা ম্যানেজড Postgres (Neon / Supabase / Railway — ফ্রি টিয়ার আছে)
> নিয়ে শুধু `DB_*` সেই সার্ভারে পয়েন্ট করুন। দ্বিতীয়টা শেয়ার্ড হোস্টিংয়ে সবচেয়ে সহজ।

**কিউ ওয়ার্কার বা ক্রন লাগে না** — অ্যাপে কোনো queued job বা scheduled task নেই।

---

## পথ ক — cPanel / DirectAdmin শেয়ার্ড হোস্টিং

আপনার সার্ভারের গঠন `~/domains/<domain>/` ধরে লেখা।

### ১. কোড আপলোড

SSH থাকলে:

```bash
cd ~/domains/leen.com.bd && git clone https://github.com/leenlifestylebd/niswan-laravel.git niswan
```

SSH না থাকলে: রিপো থেকে ZIP নামিয়ে File Manager দিয়ে `~/domains/leen.com.bd/niswan/` এ আনজিপ করুন।

> কোডটা `public_html`-এর **বাইরে** রাখুন। শুধু `public/` ফোল্ডারটা ওয়েব থেকে দেখা যাবে।

### ২. সাবডোমেইন বানিয়ে ডকুমেন্ট রুট ঠিক করুন

কন্ট্রোল প্যানেলে নতুন সাবডোমেইন (যেমন `shop.leen.com.bd`) খুলে তার
**Document Root** দিন:

```
/home/<user>/domains/leen.com.bd/niswan/public
```

ডকুমেন্ট রুট বদলাতে না পারলে `public_html/shop` থেকে symlink করুন:

```bash
ln -s ~/domains/leen.com.bd/niswan/public ~/domains/leen.com.bd/public_html/shop
```

### ৩. নির্ভরতা ইনস্টল

```bash
cd ~/domains/leen.com.bd/niswan && composer install --no-dev --optimize-autoloader
```

Composer না থাকলে লোকালি `composer install --no-dev -o` চালিয়ে `vendor/` ফোল্ডারসহ আপলোড করুন।

### ৪. ফ্রন্টএন্ড অ্যাসেট

শেয়ার্ড হোস্টিংয়ে সাধারণত Node থাকে না। **নিজের পিসিতে বিল্ড করে আপলোড করুন:**

```bash
npm run build
```

তারপর তৈরি হওয়া `public/build/` ফোল্ডারটা সার্ভারের `niswan/public/build/` এ আপলোড করুন।
(`.gitignore` এ থাকায় এটা রিপোতে যায় না — প্রতিবার ডিজাইন বদলালে আবার আপলোড করতে হবে।)

### ৫. `.env` তৈরি

```bash
cd ~/domains/leen.com.bd/niswan && cp .env.example .env && php artisan key:generate
```

`.env` এ বসান:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://shop.leen.com.bd

DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

ADMIN_PASSWORD=<প্রথম লগইনের পাসওয়ার্ড>
UPLOAD_DIR=/home/<user>/domains/leen.com.bd/niswan/storage/uploads
```

> 🔑 `php artisan key:generate` যে `APP_KEY` বানাবে সেটা **আলাদা করে সেভ করে রাখুন**।
> এটা হারালে/বদলালে সেটিংসে রাখা Steadfast, Telegram, Meta টোকেন আর ডিক্রিপ্ট হবে না।

### ৬. ডেটাবেজ ও পারমিশন

```bash
php artisan migrate --force --seed
```

```bash
chmod -R 775 storage bootstrap/cache && mkdir -p storage/uploads
```

### ৭. প্রোডাকশন ক্যাশ

```bash
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### ৮. HTTPS

কন্ট্রোল প্যানেল থেকে সাবডোমেইনের জন্য **Let's Encrypt SSL** চালু করুন, আর
"Force HTTPS" অন করুন।

---

## পথ খ — VPS + Coolify + Cloudflare Tunnel

রিপোতে `Dockerfile` ও `docker/` (nginx + php-fpm + supervisor) রেডি আছে।

1. Coolify-তে **New Resource → Dockerfile** — এই রিপো, expose port **80**।
2. Coolify-তে আলাদা **PostgreSQL** সার্ভিস বানান।
3. Environment variables:
   ```
   APP_KEY=base64:...        # php artisan key:generate --show দিয়ে বানিয়ে নিন
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://<domain>
   DB_CONNECTION=pgsql
   DB_HOST=<coolify postgres host>
   DB_PORT=5432
   DB_DATABASE=... DB_USERNAME=... DB_PASSWORD=...
   ADMIN_PASSWORD=<প্রথম লগইন>
   ```
4. **Persistent volume**: `/app/storage`
   না দিলে redeploy করলেই আপলোড করা সব ছবি মুছে যাবে।
5. **Domains** ফিল্ডে apex ও www দুটোই: `https://domain,https://www.domain`
6. Cloudflare DNS — `@` এবং `www` দুটোই **Proxied CNAME**:
   ```
   35ac07ef-c56d-4b80-bec7-26db28161178.cfargotunnel.com
   ```
   (একই tunnel, ingress catch-all — cloudflared-এ কিছু বদলাতে হবে না)

> ⚠️ VPS-এর মুম্বাই IP বাংলাদেশ থেকে blackholed। **সরাসরি A-রেকর্ড দেবেন না** —
> অবশ্যই Cloudflare Tunnel দিয়ে সার্ভ করতে হবে।

`docker/entrypoint.sh` কনটেইনার চালুর সময় নিজেই `migrate --force`, `db:seed --force`
ও তিনটা `:cache` কমান্ড চালায় — হাতে কিছু করতে হয় না।

---

## ডেপ্লয়ের পরে — চেকলিস্ট

1. `https://<domain>/admin` এ `.env` এর `ADMIN_PASSWORD` দিয়ে লগইন
2. **সেটিংস → অ্যাডমিন পাসওয়ার্ড** — এখনই বদলান (ডিফল্ট রাখবেন না)
3. **সেটিংস → ল্যান্ডিং পেজ** — ব্যানার আপলোড (ডেস্কটপ + মোবাইল আলাদা), হিরো
   হেডলাইন/সাবলাইন, কোন প্রোডাক্টটা দেখাবে
4. **সেটিংস → ব্র্যান্ড / যোগাযোগ** — নাম, ফোন, WhatsApp (কান্ট্রি কোড সহ, `+` ছাড়া:
   `8801XXXXXXXXX`), Facebook, Instagram
5. **সেটিংস → ডেলিভারি ও থিম** — ঢাকার ভিতরে/বাইরের চার্জ, রঙ
6. **সেটিংস → ইন্টিগ্রেশন** — Telegram Bot Token + Chat ID, Steadfast API Key +
   Secret, Meta Pixel ID + CAPI Token
7. **প্রোডাক্ট** — ডেমো ৫টা মুছে আসল প্রোডাক্ট যোগ করুন
8. **রিভিউ** — ডেমো মকআপ ৬টা মুছে **আসল গ্রাহকের স্ক্রিনশট** দিন
9. **টেস্ট অর্ডার দিন** — Telegram-এ নোটিফিকেশন আসে কি না দেখুন, তারপর অ্যাডমিন
   থেকে সেই অর্ডার ডিলিট/বাতিল করুন
10. **বাংলাদেশি নেটওয়ার্ক (Robi/GP) থেকে সাইট খুলে দেখুন** — বিশেষ করে পথ খ-এ

ডেমো ডেটা একসাথে মুছতে:

```bash
php artisan tinker --execute="App\Models\Visit::truncate(); App\Models\Order::truncate();"
```

---

## পরে আপডেট করতে

**পথ ক (শেয়ার্ড হোস্টিং):**

```bash
cd ~/domains/leen.com.bd/niswan && git pull && composer install --no-dev -o && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

ডিজাইন/CSS বদলালে লোকালি `npm run build` চালিয়ে `public/build/` আবার আপলোড করুন।

**পথ খ (Coolify):** রিপোতে push করলেই Coolify নিজে রিডেপ্লয় করবে।

---

## সমস্যা হলে

| উপসর্গ | কারণ / সমাধান |
|---|---|
| সাদা পেজ বা 500 | `storage/logs/laravel.log` দেখুন। `storage` ও `bootstrap/cache` এ লেখার পারমিশন (775) আছে কি না দেখুন |
| CSS/JS লোড হয় না | `public/build/` আপলোড হয়নি — লোকালি `npm run build` করে আবার দিন |
| "could not find driver" | `pdo_pgsql` এক্সটেনশন চালু নেই |
| সেটিংসের টোকেন কাজ করছে না | `APP_KEY` বদলে গেছে — অ্যাডমিন থেকে টোকেনগুলো আবার বসান |
| অ্যানালিটিক্সে SQL error | DB PostgreSQL নয় (উপরের ০ নং সেকশন দেখুন) |
| আপলোড করা ছবি হারিয়ে যায় | পথ খ-তে `/app/storage` persistent volume দেওয়া হয়নি |
| ভুল/পুরোনো কনফিগ ধরছে | `php artisan config:clear && php artisan config:cache` |
