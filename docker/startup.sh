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

# Verify and fix directory permissions if needed
echo "Verifying directory structure and permissions..."
mkdir -p /app/storage/framework/views
mkdir -p /app/storage/framework/cache/data
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/logs
mkdir -p /app/bootstrap/cache

chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache
find /app/storage -type d -exec chmod g+s {} \;
find /app/bootstrap/cache -type d -exec chmod g+s {} \;

# Process Nginx config template
envsubst '\$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Wait for Redis if URL is provided
if [ -n "$REDIS_URL" ]; then
    REDIS_HOST=$(echo $REDIS_URL | awk -F[@:] '{print $4}')
    REDIS_PORT=$(echo $REDIS_URL | awk -F[@:] '{print $5}')
    REDIS_PASSWORD=$(echo $REDIS_URL | awk -F[/@] '{print $3}' | cut -d: -f2)

    echo "Testing Redis connection to $REDIS_HOST:$REDIS_PORT"
    timeout 30 sh -c "until redis-cli --tls -h $REDIS_HOST -p $REDIS_PORT -a $REDIS_PASSWORD ping | grep -q PONG; do sleep 1; done"
fi

# Start PHP-FPM
php-fpm -D

# Laravel optimization commands
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Nginx
exec nginx -g 'daemon off;'
