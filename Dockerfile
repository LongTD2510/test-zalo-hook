FROM php:8.1-fpm

# Arguments defined in docker-compose.yml
ARG user=clcntt
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    supervisor \
    cron \
    bash \
    readline-common \
    libreadline-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install -j$(nproc) gd

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath sockets

# Verify WebP support (optional - for debugging)
RUN php -m | grep -i gd

# Create supervisor directories
RUN mkdir -p /var/log/supervisor /var/run/supervisor /etc/supervisor/conf.d

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy supervisor config
COPY ./docker/supervisord/supervisord.conf /etc/supervisor/supervisord.conf

# Copy and setup crontab
COPY ./docker/crontab/root /tmp/crontab
RUN crontab /tmp/crontab && rm /tmp/crontab

# Copy PHP config
ADD ./docker/php/php.ini /usr/local/etc/php/php.ini

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

# Start supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
