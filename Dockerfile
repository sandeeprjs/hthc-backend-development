# Stage 1: Builder Stage
FROM composer:2 AS builder

# Set working directory
WORKDIR /var/www

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies without dev dependencies
RUN composer install --no-dev --prefer-dist --no-scripts --no-interaction

# Copy the rest of the application files
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# Copy over the application .env file if needed
# COPY .env ./

# Stage 2: Production Stage
FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Copy the application from the builder stage
COPY --from=builder /var/www /var/www

# Copy nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Set file permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Expose port 8080
EXPOSE 8080

# Start PHP-FPM and Nginx
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
