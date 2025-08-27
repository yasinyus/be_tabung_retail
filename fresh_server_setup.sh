#!/bin/bash

echo "=== FRESH SERVER SETUP (SAFE) ==="
echo "This will refresh core files without losing data"

echo "1. Backing up important files..."
cp .env .env.backup
cp -r storage/app storage_app_backup
cp -r database/seeders database_seeders_backup

echo "2. Refreshing Composer dependencies..."
composer install --optimize-autoloader --no-dev

echo "3. Refreshing Filament..."
php artisan filament:install --panels --force

echo "4. Regenerating everything..."
php artisan key:generate --force
composer dump-autoload --optimize

echo "5. Clearing all caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

echo "6. Setting permissions..."
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "7. Running migrations (safe)..."
php artisan migrate --force

echo "8. Testing routes..."
php artisan route:list | grep admin

echo "✅ FRESH SETUP COMPLETE!"
echo "Test: http://your-domain/admin"

echo ""
echo "🔄 If this doesn't work, we can try full Laravel reinstall"
echo "📊 Your data is safe - only refreshed core files"
