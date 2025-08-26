#!/bin/bash

echo "=== SEEDING ALL DATA FOR FILAMENT RESOURCES ==="

# Clear cache terlebih dahulu
echo "1. Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Migrate database jika diperlukan
echo "2. Running migrations..."
php artisan migrate --force

# Seed data untuk semua resource
echo "3. Seeding User data..."
php artisan db:seed --class=UserSeeder

echo "4. Seeding Armada data..."
php artisan db:seed --class=ArmadaSeeder

echo "5. Seeding Gudang data..."
php artisan db:seed --class=GudangSeeder

echo "6. Seeding Pelanggan data..."
php artisan db:seed --class=PelangganSeeder

echo "7. Seeding Tabung data..."
php artisan db:seed --class=TabungSeeder

# Optimize untuk production
echo "8. Optimizing for production..."
composer dump-autoload --optimize
php artisan config:cache
php artisan route:cache

echo ""
echo "=== SEEDING COMPLETED ==="
echo "📊 Dashboard: Available"
echo "👥 User Management: $(php artisan tinker --execute="echo App\Models\User::count();") users"
echo "🚛 Armada Kendaraan: $(php artisan tinker --execute="echo App\Models\Armada::count();") armadas"
echo "🏠 Gudang: $(php artisan tinker --execute="echo App\Models\Gudang::count();") gudangs"
echo "👤 Pelanggan: $(php artisan tinker --execute="echo App\Models\Pelanggan::count();") pelanggans"
echo "🔥 Tabung Gas: $(php artisan tinker --execute="echo App\Models\Tabung::count();") tabungs"
echo ""
echo "✅ All resources should now have View and Edit actions!"
echo "🌐 Access your admin panel: http://your-domain/admin"
