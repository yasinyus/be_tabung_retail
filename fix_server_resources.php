<?php
// Script untuk fix missing resources di server production

echo "=== FIXING SERVER RESOURCES ===\n";

// Clear all caches
echo "1. Clearing all caches...\n";
system('php artisan config:clear');
system('php artisan route:clear');
system('php artisan cache:clear');
system('php artisan view:clear');

// Optimize for production
echo "2. Optimizing for production...\n";
system('composer dump-autoload --optimize');
system('php artisan config:cache');
system('php artisan route:cache');

// Check if all resource files exist
echo "3. Checking resource files...\n";

$resources = [
    'app/Filament/Resources/UserResource.php',
    'app/Filament/Resources/Tabungs/TabungResource.php',
    'app/Filament/Resources/Armadas/ArmadaResource.php',
    'app/Filament/Resources/Pelanggans/PelangganResource.php',
    'app/Filament/Resources/Gudangs/GudangResource.php',
];

foreach ($resources as $resource) {
    if (file_exists($resource)) {
        echo "✅ $resource - EXISTS\n";
    } else {
        echo "❌ $resource - MISSING\n";
    }
}

// Check if models exist
echo "4. Checking model files...\n";

$models = [
    'app/Models/User.php',
    'app/Models/Tabung.php',
    'app/Models/Armada.php',
    'app/Models/Pelanggan.php',
    'app/Models/Gudang.php',
];

foreach ($models as $model) {
    if (file_exists($model)) {
        echo "✅ $model - EXISTS\n";
    } else {
        echo "❌ $model - MISSING\n";
    }
}

echo "5. Testing resource discovery...\n";
system('php artisan route:list | grep filament');

echo "\n=== DONE ===\n";
echo "Sekarang coba akses admin panel lagi.\n";
echo "Jika masih tidak muncul, kemungkinan masalah ada di database atau environment.\n";
?>
