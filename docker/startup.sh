#!/bin/sh

# Process environment variables in Nginx config
envsubst '\$PORT' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Wait for Redis
if [ -n "$REDIS_URL" ]; then
    REDIS_HOST=$(echo $REDIS_URL | awk -F[@:] '{print $4}')
    REDIS_PORT=$(echo $REDIS_URL | awk -F[@:] '{print $5}')
    echo "Waiting for Redis at $REDIS_HOST:$REDIS_PORT..."
    timeout 30 sh -c "until nc -z $REDIS_HOST $REDIS_PORT; do sleep 1; done"
fi

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
echo "Starting Nginx on port ${PORT}"
exec nginx -g "daemon off;"
