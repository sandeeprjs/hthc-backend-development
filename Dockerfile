# Stage 1: Builder Stage
FROM php:8.2-fpm AS builder

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy the application code
COPY . .

# Install dependencies without dev dependencies
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Create necessary directories and set permissions
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    && chown -R www-data:www-data storage \
    && chmod -R 775 storage \
    && chown -R www-data:www-data bootstrap/cache \
    && chmod -R 775 bootstrap/cache

# Stage 2: Production Stage
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    nginx \
    libpq-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd

# Set working directory
WORKDIR /var/www

# Copy the application from the builder stage
COPY --from=builder /var/www /var/www

# Copy nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Expose port 8080
EXPOSE 8080

# Start PHP-FPM and Nginx
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
