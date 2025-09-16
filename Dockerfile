# Sử dụng PHP 8.2 + Apache
FROM php:8.2-apache

# Cài extension cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Cài Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy code vào container
WORKDIR /var/www/html
COPY . .

# Cài đặt Laravel
RUN composer install --no-dev --optimize-autoloader

# Set quyền cho storage & bootstrap
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose cổng
EXPOSE 80

# Start Laravel bằng built-in server (hoặc Apache)
CMD php artisan serve --host=0.0.0.0 --port=80