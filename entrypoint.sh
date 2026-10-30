#!/bin/bash
set -e

# Ensure all storage & database directories exist
mkdir -p /var/www/html/database
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Ensure SQLite file exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

# Ensure full permissions on storage and database
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Ensure a valid 32-byte Base64 APP_KEY exists
if [[ -z "$APP_KEY" || "$APP_KEY" != base64:* ]]; then
    export APP_KEY="base64:lZCNY3+tUGbHiANsbWPlao8Q7W7jZKdZ3Yfx4y7Ds+g="
fi

# Ensure default Indian Standard Timezone (IST)
export APP_TIMEZONE="${APP_TIMEZONE:-Asia/Kolkata}"

# Clear old compiled caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations and seed data
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction || true

# Re-cache optimizations
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Final permission ensure
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Start Apache in foreground
exec apache2-foreground
