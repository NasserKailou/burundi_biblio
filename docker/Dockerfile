FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev libicu-dev default-mysql-client \
    nodejs npm \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci --no-audit --no-fund \
    && npm run build \
    && npm cache clean --force

RUN mkdir -p storage/app/manuels storage/app/couvertures storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
