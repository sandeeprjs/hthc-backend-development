#!/bin/sh

# Wait for Redis to be available
timeout 30 sh -c 'until nc -z $(echo $REDIS_URL | cut -d "@" -f2 | cut -d ":" -f1) $(echo $REDIS_URL | cut -d ":" -f3); do sleep 1; done'

# Start services
php-fpm -D
nginx -g "daemon off;"
