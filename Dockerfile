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
    # Add required build tools
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
    # Cleanup build dependencies
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

# Copy application files
WORKDIR /app
COPY . .

# Install Composer
RUN wget -O /usr/local/bin/composer https://getcomposer.org/composer.phar \
    && chmod +x /usr/local/bin/composer

# Install dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Startup script
COPY --chmod=0755 docker/startup.sh /app/docker/startup.sh

CMD ["/app/docker/startup.sh"]
