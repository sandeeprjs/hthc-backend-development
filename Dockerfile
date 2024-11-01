FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache nginx wget bash

# Create necessary directories
RUN mkdir -p /run/nginx /var/www/html

# Copy Nginx configuration file
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy application files
COPY . /var/www/html

# Install Composer
RUN wget http://getcomposer.org/composer.phar && \
    chmod a+x composer.phar && \
    mv composer.phar /usr/local/bin/composer

# Install PHP dependencies
RUN cd /var/www/html && \
    /usr/local/bin/composer install --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Nginx and PHP-FPM
CMD ["sh", "-c", "nginx && php-fpm"]