FROM --platform=linux/amd64 laravelsail/php82-composer

# Set the working directory (assuming composer.json is in the root)
WORKDIR .

# Copy the application code
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 8080
EXPOSE 8080

# Start the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
