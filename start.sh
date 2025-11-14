#!/bin/sh
set -e

echo "=========================================="
echo "Starting Laravel Application Setup"
echo "=========================================="

# Wait for database to be ready (reduced attempts for faster startup)
echo "Waiting for database connection..."
max_attempts=10
attempt=0
while [ $attempt -lt $max_attempts ]; do
    if php -r "try { \$pdo = new PDO('pgsql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'OK'; } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
        echo "Database connection successful!"
        break
    fi
    attempt=$((attempt + 1))
    echo "Attempt $attempt/$max_attempts: Database not ready, waiting 2 seconds..."
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "WARNING: Could not connect to database after $max_attempts attempts"
    echo "Continuing anyway - migrations will fail if database is not available"
fi

# Clear all caches FIRST (important for reading fresh env vars)
echo "Clearing Laravel caches..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Run migrations (Laravel's migrate is idempotent - only runs pending migrations)
echo "Running database migrations..."
php artisan migrate --force

# Check if database needs seeding (only seed if admin user doesn't exist)
echo "Checking if database needs seeding..."
ADMIN_EXISTS=$(php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo DB::table('users')->where('username', 'admin')->exists() ? '1' : '0';
" 2>/dev/null || echo "0")

if [ "$ADMIN_EXISTS" = "0" ]; then
    echo "Seeding database..."
    echo "This will create default admin, instructor, and student accounts..."
    php artisan db:seed --class=DatabaseSeeder --force
else
    echo "Database already seeded, skipping..."
fi

# Create storage link (idempotent)
echo "Creating storage link..."
php artisan storage:link 2>/dev/null && echo "Storage link created" || echo "Storage link already exists or creation skipped"

# Set proper permissions
echo "Setting file permissions..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Optimize for production (AFTER everything is set up)
echo "Optimizing for production..."
php artisan config:cache 2>/dev/null || {
    echo "WARNING: Config cache failed, using live config"
}
php artisan route:cache 2>/dev/null || {
    echo "WARNING: Route cache failed, using live routes"
}
php artisan view:cache 2>/dev/null || {
    echo "WARNING: View cache failed, using live views"
}

echo "=========================================="
echo "Starting Laravel server on port ${PORT:-8000}"
echo "=========================================="

# Start the server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
