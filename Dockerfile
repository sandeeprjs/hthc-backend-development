# Dockerfile
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk update && apk add --no-cache \
    nginx \
    wget \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zlib-dev \
    libsodium-dev \
    curl-dev \
    gettext \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
        gd \
        zip \
        sodium \
        pdo_mysql \
        curl \
        opcache \
        pcntl \
    && apk del $PHPIZE_DEPS

# Install Redis extension
RUN apk add --no-cache \
    hiredis-dev \
    $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Configure nginx
RUN mkdir -p /run/nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf.template

# Configure PHP-FPM
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copy application files
WORKDIR /app
COPY . .

# Install Composer
RUN wget -O /usr/local/bin/composer https://getcomposer.org/composer.phar \
    && chmod +x /usr/local/bin/composer

# Install dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Set up directory structure and permissions exactly as specified
RUN mkdir -p /app/storage/logs \
    && mkdir -p /app/storage/framework/{sessions,views,cache} \
    && mkdir -p /app/bootstrap/cache \
    && chgrp -R www-data /app/bootstrap /app/storage /app/storage/logs \
    && chmod -R 755 /app/bootstrap /app/storage /app/storage/logs \
    && chmod -R g+w /app/bootstrap /app/storage /app/storage/logs \
    && find /app/bootstrap -type d -exec chmod g+s {} + \
    && find /app/storage -type d -exec chmod g+s {} + \
    && find /app/storage/logs -type d -exec chmod g+s {} + \
    && touch /app/storage/logs/laravel.log \
    && chown www-data:www-data /app/storage/logs/laravel.log \
    && chmod 664 /app/storage/logs/laravel.log


# Copy and set startup script permissions
COPY docker/startup.sh /app/docker/startup.sh
RUN chmod +x /app/docker/startup.sh

CMD ["/app/docker/startup.sh"]
