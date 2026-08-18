# ── ফ্রন্টএন্ড অ্যাসেট বিল্ড (Tailwind + Alpine) ────────────────────────────
FROM node:22-alpine AS assets
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# ── PHP নির্ভরতা ──────────────────────────────────────────────────────────
FROM composer:2 AS vendor
WORKDIR /build
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --optimize --no-dev

# ── রানটাইম: php-fpm + nginx ──────────────────────────────────────────────
FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor postgresql-dev icu-dev libzip-dev oniguruma-dev \
 && docker-php-ext-install pdo_pgsql pgsql intl zip opcache bcmath \
 && docker-php-ext-enable opcache

WORKDIR /app

COPY --from=vendor /build /app
COPY --from=assets /build/public/build /app/public/build

# আপলোড ডিরেক্টরি — Coolify-তে এখানে persistent volume mount করতে হবে,
# নাহলে redeploy-এ আপলোড করা ছবি মুছে যাবে।
RUN mkdir -p /app/storage/uploads /app/storage/framework/{cache,sessions,views} /app/storage/logs /app/bootstrap/cache \
 && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
 && chmod -R 775 /app/storage /app/bootstrap/cache

COPY docker/nginx.conf     /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh  /usr/local/bin/entrypoint.sh
COPY docker/php.ini        /usr/local/etc/php/conf.d/99-app.ini
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
