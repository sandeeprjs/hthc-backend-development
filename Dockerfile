# Use the official PHP image with Apache
FROM php:8.0-apache

# Install necessary system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite for Laravel
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/html/

# Install Composer and Laravel dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Copy existing application directory contents
COPY . /var/www/html

# Copy Apache vhost configuration file for Laravel
COPY ./docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Ensure storage and bootstrap cache are writable
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Set environment variable for Cloud Run
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_URL=http://localhost

# Run artisan commands for setup
RUN php artisan key:generate && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expose port 8080 for Cloud Run
EXPOSE 8080

# Start Apache in the foreground
CMD ["apache2-foreground"]
