# Use PHP 8.2 CLI (works for both web server and worker)
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install NPM dependencies and build assets
RUN npm install && npm run build

# Configure PHP
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory.ini \
    && echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/upload.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/upload.ini

# Expose port (default 8000, but Render will use $PORT)
EXPOSE 8000

# Default command (can be overridden by startCommand in render.yaml)
# For web: php artisan serve --host=0.0.0.0 --port=$PORT
# For worker: php artisan queue:work --sleep=3 --tries=3
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

