# Stage 1: Build Stage
FROM composer:2 AS build

# Set working directory
WORKDIR /app

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy the rest of the application code
COPY . .

# Stage 2: Production Stage
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update -y && \
    apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        zip \
        unzip \
        git \
        curl && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd

# Copy the custom Apache configuration
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Update Apache ports configuration to listen on port 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www

# Copy the application from the build stage
COPY --from=build /app /var/www

# Set permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Expose port 8080
EXPOSE 8080

# Start Apache in the foreground
CMD ["apache2-foreground"]
