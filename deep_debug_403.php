<?php

// Bootstrap Laravel untuk script ini
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEEP DEBUG 403 FORBIDDEN ===\n\n";

// 1. Check if Filament is properly installed
echo "1️⃣  Checking Filament Installation...\n";
$composerJson = json_decode(file_get_contents('composer.json'), true);
$filamentVersion = $composerJson['require']['filament/filament'] ?? 'NOT FOUND';
echo "   Filament version: {$filamentVersion}\n";

// Check if vendor/filament exists
if (is_dir('vendor/filament')) {
    echo "   ✅ Filament vendor directory exists\n";
} else {
    echo "   ❌ Filament vendor directory missing!\n";
    echo "   🔧 Run: composer install\n";
}

// 2. Check web server and PHP
echo "\n2️⃣  Checking Environment...\n";
echo "   PHP version: " . phpversion() . "\n";
try {
    echo "   Laravel version: " . app()->version() . "\n";
} catch (Exception $e) {
    echo "   Laravel version: ERROR - {$e->getMessage()}\n";
}

// 3. Check file permissions
echo "\n3️⃣  Checking File Permissions...\n";
$checkPaths = [
    'storage/',
    'bootstrap/cache/',
    'app/Providers/Filament/AdminPanelProvider.php',
];

foreach ($checkPaths as $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "   {$path}: {$perms}\n";
    } else {
        echo "   {$path}: NOT FOUND\n";
    }
}

// 4. Check if routes are working
echo "\n4️⃣  Checking Routes...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = 0;
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'admin')) {
            $adminRoutes++;
        }
    }
    
    echo "   Total routes: " . count($routes) . "\n";
    echo "   Admin routes: {$adminRoutes}\n";
    
    if ($adminRoutes > 0) {
        echo "   ✅ Admin routes registered\n";
    } else {
        echo "   ❌ No admin routes found!\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error checking routes: {$e->getMessage()}\n";
}

// 5. Check .env configuration
echo "\n5️⃣  Checking .env Configuration...\n";
$envVars = [
    'APP_ENV',
    'APP_DEBUG',
    'APP_URL',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_DATABASE',
];

foreach ($envVars as $var) {
    $value = env($var, 'NOT SET');
    echo "   {$var}: {$value}\n";
}

// 6. Test basic HTTP response
echo "\n6️⃣  Testing Basic Response...\n";
try {
    $appUrl = env('APP_URL', 'http://localhost');
    echo "   App URL: {$appUrl}\n";
    
    // Skip HTTP test in script untuk avoid issues
    echo "   ⚠️  HTTP test skipped (run manually)\n";
    echo "   Manual test: curl {$appUrl}\n";
} catch (Exception $e) {
    echo "   ❌ URL test failed: {$e->getMessage()}\n";
}

// 7. Check for common blocking issues
echo "\n7️⃣  Checking Common Issues...\n";

// Check if .htaccess exists (Apache)
if (file_exists('public/.htaccess')) {
    echo "   ✅ .htaccess found\n";
} else {
    echo "   ⚠️  .htaccess missing (may need for Apache)\n";
}

// Check if storage is linked
if (is_link('public/storage')) {
    echo "   ✅ Storage linked\n";
} else {
    echo "   ⚠️  Storage not linked (run: php artisan storage:link)\n";
}

echo "\n🎯 RECOMMENDATIONS:\n";
echo "1. 🚀 Try nuclear option: php nuclear_option_fix.php\n";
echo "2. 🔧 Check web server error logs\n";
echo "3. 📝 Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "4. 🌐 Test direct URL: http://your-domain/admin (not https)\n";
echo "5. 🔄 Restart web server (Apache/Nginx)\n";

?>
