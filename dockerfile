FROM php:8.2-fpm

# Install deps
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ✅ copy المشروع كامل (فيه artisan)
COPY . .

# ✅ install deps
RUN composer install --prefer-dist --no-interaction --no-progress

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000