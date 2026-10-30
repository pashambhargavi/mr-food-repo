#!/bin/bash
set -e

# Ensure SQLite file exists
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi
chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database

# Run Laravel migrations and seeders if needed
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction || true

# Cache configurations for speed
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Start Apache in foreground
exec apache2-foreground
