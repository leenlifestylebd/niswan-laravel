#!/bin/sh
set -e

# আপলোড ডিরেক্টরি (persistent volume) নিশ্চিত করো
mkdir -p "${UPLOAD_DIR:-/app/storage/uploads}"
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true

# APP_KEY না থাকলে চলবে না — Crypt দিয়ে সেভ করা টোকেন ডিক্রিপ্ট হবে না
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY সেট করা নেই! Coolify-র env এ 'php artisan key:generate --show' এর মান বসাও।"
fi

# মাইগ্রেশন (নতুন DB হলে টেবিল তৈরি) + প্রথমবার ডেমো ডেটা
php artisan migrate --force || true
php artisan db:seed --force || true

# প্রোডাকশন ক্যাশ
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
