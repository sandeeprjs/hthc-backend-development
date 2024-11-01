#!/bin/sh

# Substitute PORT in Nginx config
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Start PHP-FPM
php-fpm

# Start Nginx in the foreground
nginx -g 'daemon off;'
