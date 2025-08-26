#!/bin/bash

echo "=== SERVER DEPLOYMENT FIX ==="

# 1. Run migrations to ensure role column exists
echo "1️⃣  Running migrations..."
php artisan migrate --force

# 2. Seed users for login
echo "2️⃣  Creating admin users..."
php artisan db:seed --class=UserSeeder --force

# 3. Clear all caches
echo "3️⃣  Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# 4. Update autoload
echo "4️⃣  Updating autoload..."
composer dump-autoload --optimize

# 5. Fix storage permissions
echo "5️⃣  Fixing storage permissions..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# 6. Create storage link
echo "6️⃣  Creating storage link..."
php artisan storage:link

# 7. Cache for production
echo "7️⃣  Caching for production..."
php artisan config:cache
php artisan route:cache

echo ""
echo "🎉 SERVER FIX COMPLETED!"
echo ""
echo "✅ Now you can login with:"
echo "📧 Email: admin@ptgas.com"
echo "🔑 Password: password123"
echo ""
echo "🌐 Access: http://your-domain/admin"
