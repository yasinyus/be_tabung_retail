#!/bin/bash

echo "=== COMPLETE FILAMENT FIX SCRIPT ==="

# 1. Clear all caches
echo "1️⃣  Clearing all caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# 2. Update autoload
echo "2️⃣  Updating autoload..."
composer dump-autoload --optimize

# 3. Fix QR codes
echo "3️⃣  Fixing QR codes..."
php artisan qr:fix

# 4. Check storage permissions
echo "4️⃣  Checking storage permissions..."
chmod -R 755 storage/
chmod -R 775 storage/app/
chmod -R 775 storage/framework/
chmod -R 775 storage/logs/

# 5. Create storage link if not exists
echo "5️⃣  Creating storage link..."
php artisan storage:link

# 6. Run debug script
echo "6️⃣  Running resource debug..."
php debug_filament_resources.php

# 7. Cache for production
echo "7️⃣  Optimizing for production..."
php artisan config:cache
php artisan route:cache

echo ""
echo "🎉 COMPLETE FIX DONE!"
echo ""
echo "✅ Expected results:"
echo "📱 Admin Panel: http://your-domain/admin"
echo "🔘 Create Button: Top right of each resource list"
echo "👁️ View Button: Blue eye icon in each row"  
echo "✏️ Edit Button: Orange pencil icon in each row"
echo "🔄 QR Code Button: Blue QR icon in each row (Tabung, Armada, Pelanggan, Gudang)"
echo "🗑️ Delete Button: Red trash icon (Users only)"
echo ""
echo "🚀 All resources should now be fully functional!"
