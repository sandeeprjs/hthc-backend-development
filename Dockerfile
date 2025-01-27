# Use PHP 8.3 with Alpine Linux
FROM php:8.3-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk update && apk add --no-cache \
    nginx \
    wget \
    git \
    unzip \
    # GD dependencies
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    # Zip extension dependencies
    libzip-dev \
    zlib-dev \
    # Other dependencies
    libsodium-dev \
    curl-dev \
    # Configure and install extensions
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
    # Cleanup
    && rm -rf /var/cache/apk/* /tmp/*

RUN apk add --no-cache \
    # Add Redis-related dependencies
    redis \
    hiredis-dev \
    && docker-php-ext-install redis \

# Configure nginx
RUN mkdir -p /run/nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Set working directory
WORKDIR /app

# Install Composer
RUN wget -O /usr/local/bin/composer https://getcomposer.org/composer.phar \
    && chmod +x /usr/local/bin/composer

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Fix permissions
RUN chown -R www-data:www-data /app

# Startup command
CMD ["sh", "/app/docker/startup.sh"]
