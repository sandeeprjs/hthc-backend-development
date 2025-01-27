# Changes to Dockerfile
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

# Set up storage structure and permissions
RUN mkdir -p /app/storage/framework/{sessions,views,cache} \
    && mkdir -p /app/storage/logs \
    && mkdir -p /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Startup script
COPY --chmod=0755 docker/startup.sh /app/docker/startup.sh

CMD ["/app/docker/startup.sh"]

# Changes to startup.sh
#!/bin/sh

# Create .env file from environment variables
cat <<EOF > /app/.env
APP_NAME=${APP_NAME:-Laravel}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=${LOG_CHANNEL:-stack}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=${BROADCAST_DRIVER:-log}
CACHE_DRIVER=${CACHE_DRIVER:-redis}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-redis}
SESSION_DRIVER=${SESSION_DRIVER:-redis}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

REDIS_URL=${REDIS_URL}
REDIS_CLIENT=${REDIS_CLIENT:-predis}

MAIL_MAILER=${MAIL_MAILER:-log}
MAIL_HOST=${MAIL_HOST}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
MAIL_FROM_NAME="${MAIL_FROM_NAME:-${APP_NAME}}"
EOF

# Ensure storage structure exists and has correct permissions
ensure_storage_structure() {
    echo "Ensuring storage structure exists..."
    mkdir -p /app/storage/framework/{sessions,views,cache}
    mkdir -p /app/storage/logs
    mkdir -p /app/bootstrap/cache

    echo "Setting correct permissions..."
    chmod -R 775 /app/storage /app/bootstrap/cache
    chown -R www-data:www-data /app/storage /app/bootstrap/cache
}

ensure_storage_structure

# Process Nginx config template
envsubst '\$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Wait for Redis with SSL support
if [ -n "$REDIS_URL" ]; then
    REDIS_HOST=$(echo $REDIS_URL | awk -F[@:] '{print $4}')
    REDIS_PORT=$(echo $REDIS_URL | awk -F[@:] '{print $5}')
    REDIS_PASSWORD=$(echo $REDIS_URL | awk -F[/@] '{print $3}' | cut -d: -f2)

    echo "Testing Redis connection to $REDIS_HOST:$REDIS_PORT"
    timeout 30 sh -c "until redis-cli --tls -h $REDIS_HOST -p $REDIS_PORT -a $REDIS_PASSWORD ping | grep -q PONG; do sleep 1; done"
fi

# Start PHP-FPM
php-fpm -D

# Verify storage permissions before cache commands
if [ ! -w "/app/storage/framework/views" ]; then
    echo "Warning: Views directory not writable, fixing permissions..."
    ensure_storage_structure
fi

# Clear and cache Laravel configurations
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Nginx
exec nginx -g 'daemon off;'
