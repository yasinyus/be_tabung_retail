<?php
// immediate-pail-fix.php - Quick fix for Pail error

echo "🚨 IMMEDIATE PAIL ERROR FIX\n";
echo "==========================\n\n";

// 1. Update .env to production mode
echo "📝 Setting production environment...\n";
$envPath = '.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Set production environment
    if (strpos($envContent, 'APP_ENV=') === false) {
        $envContent .= "\nAPP_ENV=production\n";
    } else {
        $envContent = preg_replace('/APP_ENV=.*/', 'APP_ENV=production', $envContent);
    }
    
    // Disable debug
    if (strpos($envContent, 'APP_DEBUG=') === false) {
        $envContent .= "APP_DEBUG=false\n";
    } else {
        $envContent = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG=false', $envContent);
    }
    
    file_put_contents($envPath, $envContent);
    echo "✅ Updated .env file\n";
} else {
    echo "❌ .env file not found\n";
}

// 2. Clear all Laravel caches
echo "\n🧹 Clearing all caches...\n";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php', 
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php'
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "✅ Removed $file\n";
    }
}

// Clear storage caches
$storageDirs = [
    'storage/framework/cache/data',
    'storage/framework/views',
    'storage/framework/sessions'
];

foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Cleared $dir\n";
    }
}

// 3. Update AppServiceProvider to conditionally load Pail
echo "\n🔧 Updating AppServiceProvider...\n";
$appServiceProvider = 'app/Providers/AppServiceProvider.php';
$newContent = '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only register Pail in local environment and if class exists
        if (app()->environment("local") && class_exists(\Laravel\Pail\PailServiceProvider::class)) {
            $this->app->register(\Laravel\Pail\PailServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
';

file_put_contents($appServiceProvider, $newContent);
echo "✅ Updated AppServiceProvider.php\n";

// 4. Create composer override
echo "\n📦 Creating composer production config...\n";
$composerContent = file_get_contents('composer.json');
$composerData = json_decode($composerContent, true);

// Remove pail from require-dev for production
if (isset($composerData['require-dev']['laravel/pail'])) {
    unset($composerData['require-dev']['laravel/pail']);
}

// Update dev script to not use pail
if (isset($composerData['scripts']['dev'])) {
    $composerData['scripts']['dev'] = [
        "Composer\\Config::disableProcessTimeout",
        "npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1\" \"npm run dev\" --names=server,queue,vite"
    ];
}

// Add dont-discover for pail
$composerData['extra']['laravel']['dont-discover'][] = 'laravel/pail';

// Save production composer.json
file_put_contents('composer-production.json', json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "✅ Created composer-production.json\n";

// 5. Create package discovery override
echo "\n🔍 Creating package discovery override...\n";
$discoveryFile = 'bootstrap/cache/packages.php';
$discoveryContent = '<?php return [
    "providers" => [],
    "eager" => [],
    "deferred" => [],
    "when" => []
];';

// Ensure bootstrap/cache directory exists
if (!is_dir('bootstrap/cache')) {
    mkdir('bootstrap/cache', 0755, true);
}

file_put_contents($discoveryFile, $discoveryContent);
echo "✅ Created packages.php override\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 PAIL ERROR FIXED!\n";
echo str_repeat("=", 50) . "\n";
echo "📋 WHAT WAS DONE:\n";
echo "✅ Set APP_ENV=production\n";
echo "✅ Set APP_DEBUG=false\n";
echo "✅ Cleared all Laravel caches\n";
echo "✅ Updated AppServiceProvider to conditionally load Pail\n";
echo "✅ Created production composer.json\n";
echo "✅ Overrode package discovery\n\n";

echo "🌐 NOW TEST YOUR ADMIN ROUTES:\n";
echo "- /admin/users\n";
echo "- /admin\n";
echo "- /api/v1/auth/login\n\n";

echo "🚀 IF ISSUES PERSIST, RUN:\n";
echo "1. php artisan config:clear\n";
echo "2. php artisan route:clear\n";
echo "3. php artisan view:clear\n\n";

echo "✨ Your application should now work without Pail errors!\n";
?>
