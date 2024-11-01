# Use PHP 8.2 FPM as the base image
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update -y && apt-get install -y \
    build-essential \
    autoconf \
    pkg-config \
    libssl-dev \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql mbstring zip gd

# Set the working directory
WORKDIR /app

# Copy the application code
COPY . /app

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 8080
EXPOSE 8080

# Start the application using the PORT environment variable
CMD ["php-fpm"]
