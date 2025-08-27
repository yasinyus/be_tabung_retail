<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FIX FILAMENT ROUTES ===\n\n";

// 1. Clear all caches first
echo "1️⃣  Clearing All Caches...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "   ✅ Config cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "   ✅ Route cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "   ✅ Application cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "   ✅ View cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "   ✅ All optimization cleared\n";
    
} catch (Exception $e) {
    echo "   ❌ Cache clear error: {$e->getMessage()}\n";
}

// 2. Check providers registration
echo "\n2️⃣  Checking Providers Registration...\n";

// Check bootstrap/providers.php
if (file_exists('bootstrap/providers.php')) {
    $providers = include 'bootstrap/providers.php';
    echo "   Providers found: " . count($providers) . "\n";
    
    foreach ($providers as $provider) {
        echo "   - {$provider}\n";
        
        if (strpos($provider, 'AdminPanelProvider') !== false) {
            echo "     ✅ AdminPanelProvider found!\n";
        }
    }
} else {
    echo "   ❌ bootstrap/providers.php not found!\n";
}

// 3. Force refresh discovery
echo "\n3️⃣  Force Package Discovery...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('package:discover');
    echo "   ✅ Package discovery completed\n";
    
} catch (Exception $e) {
    echo "   ⚠️  Package discovery: {$e->getMessage()}\n";
}

// 4. Check if Filament is properly loaded
echo "\n4️⃣  Checking Filament Installation...\n";
try {
    if (class_exists('Filament\Filament')) {
        echo "   ✅ Filament class exists\n";
    } else {
        echo "   ❌ Filament class not found!\n";
    }
    
    if (class_exists('App\Providers\Filament\AdminPanelProvider')) {
        echo "   ✅ AdminPanelProvider class exists\n";
    } else {
        echo "   ❌ AdminPanelProvider class not found!\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Class check error: {$e->getMessage()}\n";
}

// 5. Check routes after clearing cache
echo "\n5️⃣  Checking Routes Registration...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = [];
    $loginRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        
        if (str_contains($uri, 'admin')) {
            $adminRoutes[] = $uri;
        }
        
        if (str_contains($uri, 'login')) {
            $loginRoutes[] = $uri;
        }
    }
    
    echo "   Total routes: " . count($routes) . "\n";
    echo "   Admin routes: " . count($adminRoutes) . "\n";
    echo "   Login routes: " . count($loginRoutes) . "\n";
    
    if (count($adminRoutes) > 0) {
        echo "   ✅ Admin routes found:\n";
        foreach (array_slice($adminRoutes, 0, 5) as $route) {
            echo "     - {$route}\n";
        }
    } else {
        echo "   ❌ No admin routes found!\n";
    }
    
    if (count($loginRoutes) > 0) {
        echo "   ✅ Login routes found:\n";
        foreach ($loginRoutes as $route) {
            echo "     - {$route}\n";
        }
    } else {
        echo "   ❌ No login routes found!\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Routes check error: {$e->getMessage()}\n";
}

// 6. Generate route list file for inspection
echo "\n6️⃣  Generating Route List...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('route:list', [
        '--name' => 'filament'
    ]);
    
    echo "   ✅ Route list generated\n";
    echo "   Run: php artisan route:list | grep admin\n";
    
} catch (Exception $e) {
    echo "   ⚠️  Route list: {$e->getMessage()}\n";
}

echo "\n🔧 TROUBLESHOOTING STEPS:\n";
echo "1. composer dump-autoload --optimize\n";
echo "2. php artisan filament:install --panels\n";
echo "3. php artisan route:list | grep admin\n";
echo "4. Check web server error logs\n";

echo "\n🎯 EXPECTED RESULTS:\n";
echo "✅ Should see admin/login in route list\n";
echo "✅ /admin should redirect to /admin/login\n";
echo "✅ /admin/login should show login form\n";

?>
