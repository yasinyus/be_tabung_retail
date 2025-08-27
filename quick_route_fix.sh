#!/bin/bash

echo "=== QUICK ROUTE FIX ==="

echo "1. Composer optimize..."
composer dump-autoload --optimize

echo "2. Clear all caches..."
php artisan config:clear
php artisan route:clear  
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

echo "3. Package discovery..."
php artisan package:discover

echo "4. Filament install..."
php artisan filament:install --panels --quiet

echo "5. Check routes..."
echo "Admin routes:"
php artisan route:list | grep admin

echo ""
echo "=== TEST URLS ==="
echo "Try: http://8.215.70.68/admin"
echo "Try: http://8.215.70.68/admin/login"
echo "Try: http://8.215.70.68/index.php/admin"

echo ""
echo "=== IF STILL NOT WORKING ==="
echo "1. Check web server error logs"
echo "2. Check storage/logs/laravel.log"
echo "3. Try: php artisan serve (test with built-in server)"
