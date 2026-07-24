# ─────────────────────────────────────────
# Stage 1: Composer Build & Auto-loader
# ─────────────────────────────────────────
FROM composer:2.7 AS composer_stage

WORKDIR /app
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ─────────────────────────────────────────
# Stage 2: Runtime PHP 8.3-FPM Production
# ─────────────────────────────────────────
FROM php:8.3-fpm-alpine

# Install System Dependencies (incluindo Nginx e Supervisor)
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    postgresql-dev \
    openssl \
    supervisor \
    nginx \
    tesseract-ocr

# Configure and Install PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        bcmath \
        mbstring \
        exif \
        pcntl \
        opcache

# PECL Redis Extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Copy Custom PHP & OPcache Config
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copy Nginx & Supervisor Config
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy Entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www

# Copy Application Artifacts
COPY --from=composer_stage /app /var/www

# Create Logs & Runtime Directories with Permissions
RUN mkdir -p /var/log/php /var/log/supervisor /var/log/nginx /run/nginx /var/www/storage/logs \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/log/php /var/log/nginx /run/nginx \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 80 8080 9000

CMD ["/bin/sh", "/usr/local/bin/entrypoint.sh"]
