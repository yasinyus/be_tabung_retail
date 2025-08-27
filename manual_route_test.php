<?php

echo "=== MANUAL ROUTE TEST ===\n\n";

// Test tanpa bootstrap Laravel dulu
echo "1️⃣  Basic File Checks...\n";

$requiredFiles = [
    'vendor/filament/filament/src/FilamentServiceProvider.php' => 'Filament ServiceProvider',
    'app/Providers/Filament/AdminPanelProvider.php' => 'Admin Panel Provider',
    'bootstrap/providers.php' => 'Providers Config',
];

foreach ($requiredFiles as $file => $desc) {
    if (file_exists($file)) {
        echo "   ✅ {$desc}: EXISTS\n";
    } else {
        echo "   ❌ {$desc}: MISSING\n";
    }
}

// Check composer.json
echo "\n2️⃣  Composer Dependencies...\n";
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    
    $filamentVersion = $composer['require']['filament/filament'] ?? 'NOT FOUND';
    echo "   Filament version: {$filamentVersion}\n";
    
    if (isset($composer['require']['filament/filament'])) {
        echo "   ✅ Filament package installed\n";
    } else {
        echo "   ❌ Filament package not in composer.json!\n";
    }
}

// Check .env
echo "\n3️⃣  Environment Check...\n";
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    
    if (strpos($envContent, 'APP_KEY=') !== false) {
        echo "   ✅ APP_KEY found in .env\n";
    } else {
        echo "   ❌ APP_KEY missing in .env!\n";
    }
    
    if (strpos($envContent, 'APP_URL=') !== false) {
        echo "   ✅ APP_URL found in .env\n";
    } else {
        echo "   ⚠️  APP_URL missing in .env\n";
    }
} else {
    echo "   ❌ .env file missing!\n";
}

echo "\n🔧 MANUAL FIXES:\n";
echo "1. composer install --optimize-autoloader\n";
echo "2. php artisan key:generate (if APP_KEY missing)\n";
echo "3. php artisan filament:install --panels\n";
echo "4. php artisan config:clear && php artisan route:clear\n";
echo "5. php artisan route:list | grep admin\n";

echo "\n📝 ALSO CHECK:\n";
echo "1. Web server error logs\n";
echo "2. PHP error logs\n"; 
echo "3. Laravel logs: storage/logs/laravel.log\n";

echo "\n🎯 IF ROUTES STILL NOT WORKING:\n";
echo "1. There might be a web server configuration issue\n";
echo "2. Check if .htaccess is working (try: /index.php/admin)\n";
echo "3. Verify Filament is compatible with PHP version\n";
echo "4. Check if all required PHP extensions are installed\n";

?>
