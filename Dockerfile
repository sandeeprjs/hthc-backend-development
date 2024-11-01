# Base image with PHP 8.0 and extensions needed for Laravel
FROM php:8.0-fpm

# Install Nginx
RUN apt-get update && apt-get install -y nginx

# Install PHP extensions for Laravel
RUN apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Copy Nginx configuration file
COPY nginx.conf /etc/nginx/nginx.conf

# Expose port 8080 for Cloud Run
EXPOSE 8080

# Start both Nginx and PHP-FPM services
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
