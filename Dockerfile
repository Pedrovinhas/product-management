FROM php:8.4-fpm-alpine AS builder

WORKDIR /var/www/html

RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    bcmath \
    zip \
    intl \
    pcntl \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY backend/composer.json backend/composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist \
    --no-scripts

COPY backend/ .

RUN composer run-script post-autoload-dump

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

# Stage 1.5: frontend-builder — builds Vue app assets
FROM node:24-alpine AS frontend-builder

WORKDIR /app

COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci

COPY frontend/ ./
RUN npm run build

# Stage 2: runner — lean production PHP-FPM image
#   No Composer, no build tools — smallest possible footprint.
FROM php:8.4-fpm-alpine AS runner

WORKDIR /var/www/html

# Install only runtime system dependencies
RUN apk add --no-cache \
    libpq \
    libzip \
    icu-libs \
    oniguruma

COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d    /usr/local/etc/php/conf.d

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini

COPY --from=builder /var/www/html/vendor ./vendor

COPY backend/ .

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

# Stage 3: web — nginx with static assets baked in
#   Used as the production nginx image pushed to Docker Hub.
#   In dev, docker-compose.override.yml bind-mounts ./backend instead.
FROM nginx:1.27-alpine AS web

COPY --from=runner /var/www/html/public /var/www/html/public
COPY --from=frontend-builder /app/dist /var/www/frontend
COPY docker/nginx/prod.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]