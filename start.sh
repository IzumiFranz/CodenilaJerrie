#!/bin/sh
set -e

echo "Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Running database migrations..."
php artisan migrate --force

echo "Creating storage link..."
php artisan storage:link || true

echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

