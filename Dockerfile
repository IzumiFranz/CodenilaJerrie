# Stage 1: Dependencies
FROM php:8.2-cli AS dependencies

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev libjpeg-dev libfreetype6-dev libwebp-dev libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js (using NodeSource repository for reliability)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy only dependency files first (for better caching)
COPY composer.json composer.lock* ./
COPY package.json package-lock.json* ./

# Install PHP dependencies (cached if composer files don't change)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install NPM dependencies (cached if package files don't change)
# Use npm ci for faster, reproducible installs
# Install all dependencies (including dev) needed for building assets
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi

# Stage 2: Build assets
FROM dependencies AS build

# Copy source files needed for building
COPY . .

# Build assets
RUN npm run build

# Stage 3: Production
FROM php:8.2-cli AS production

WORKDIR /var/www/html

# Install minimal system dependencies for runtime
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    libjpeg-dev libfreetype6-dev libwebp-dev libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip opcache

# Copy Composer from dependencies stage
COPY --from=dependencies /usr/bin/composer /usr/bin/composer

# Copy vendor from dependencies stage
COPY --from=dependencies /var/www/html/vendor ./vendor

# Copy built assets from build stage (node_modules not needed in production)
COPY --from=build /var/www/html/public/build ./public/build

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure PHP
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory.ini \
    && echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/upload.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/upload.ini

# Expose port (default 8000, but Render will use $PORT)
EXPOSE 8000

# Copy and set up startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Default command (can be overridden by startCommand in render.yaml)
# For web: php artisan serve --host=0.0.0.0 --port=$PORT
# For worker: php artisan queue:work --sleep=3 --tries=3
CMD ["/start.sh"]
