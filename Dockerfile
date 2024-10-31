FROM php:8.1-fpm-alpine

# Install essential packages and PHP extensions
RUN apk add --no-cache nginx wget git zip unzip \
    && docker-php-ext-install pdo pdo_mysql opcache

# Create required directories and copy configuration
RUN mkdir -p /run/nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Set working directory
WORKDIR /app

# Copy application files to /app
COPY . /app

# Download Composer
RUN wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer

# Run Composer and save log for error inspection
RUN composer install --no-dev --verbose > /app/composer_install.log || cat /app/composer_install.log

# Set ownership for the app directory
RUN chown -R www-data: /app

# Configure php-fpm to listen on port 8000
RUN sed -i 's|listen = 127.0.0.1:9000|listen = 127.0.0.1:8000|' /usr/local/etc/php-fpm.d/www.conf

# Expose port 8000 for Cloud Run
EXPOSE 8000

# Start both php-fpm and nginx services
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
