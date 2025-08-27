<?php

echo "=== TEST BASIC LARAVEL ACCESS ===\n\n";

// Test if basic Laravel is working
echo "🔍 Testing Basic Laravel Functionality...\n\n";

try {
    // Bootstrap Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "✅ Laravel bootstrap: SUCCESS\n";
    
    // Test database connection
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "✅ Database connection: SUCCESS\n";
    } catch (Exception $e) {
        echo "❌ Database connection: FAILED - {$e->getMessage()}\n";
    }
    
    // Test basic routes
    try {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        echo "✅ Routes loaded: " . count($routes) . " routes\n";
        
        $adminRoutes = 0;
        foreach ($routes as $route) {
            if (str_contains($route->uri(), 'admin')) {
                $adminRoutes++;
            }
        }
        echo "✅ Admin routes: {$adminRoutes} routes\n";
        
    } catch (Exception $e) {
        echo "❌ Routes: FAILED - {$e->getMessage()}\n";
    }
    
    // Test Filament
    try {
        if (class_exists('Filament\Filament')) {
            echo "✅ Filament classes: LOADED\n";
        } else {
            echo "❌ Filament classes: NOT LOADED\n";
        }
    } catch (Exception $e) {
        echo "❌ Filament test: FAILED - {$e->getMessage()}\n";
    }
    
    // Test Users
    try {
        $userCount = \App\Models\User::count();
        echo "✅ Users in database: {$userCount}\n";
        
        $adminUsers = \App\Models\User::whereIn('role', ['admin_utama', 'admin_umum'])->count();
        echo "✅ Admin users: {$adminUsers}\n";
        
    } catch (Exception $e) {
        echo "❌ User test: FAILED - {$e->getMessage()}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap: FAILED - {$e->getMessage()}\n";
    echo "\n🔧 This indicates a fundamental Laravel issue!\n";
    echo "Try:\n";
    echo "1. composer install\n";
    echo "2. php artisan key:generate\n";
    echo "3. Check .env file configuration\n";
    exit(1);
}

echo "\n🌐 URL TESTS TO TRY:\n";
echo "If Laravel is working, test these URLs:\n\n";

$baseUrl = "http://8.215.70.68";
$urls = [
    "Basic Laravel (should show welcome)" => "{$baseUrl}/",
    "Laravel with index.php" => "{$baseUrl}/index.php",
    "Admin direct" => "{$baseUrl}/admin", 
    "Admin with index.php" => "{$baseUrl}/index.php/admin",
    "Admin users direct" => "{$baseUrl}/admin/users",
    "Admin users with index.php" => "{$baseUrl}/index.php/admin/users"
];

foreach ($urls as $description => $url) {
    echo "📋 {$description}:\n";
    echo "   {$url}\n\n";
}

echo "🎯 TESTING STRATEGY:\n";
echo "1. First test basic Laravel URL to confirm it works\n";
echo "2. If basic Laravel works but /admin doesn't, it's a routing issue\n";  
echo "3. If /index.php/admin works but /admin doesn't, it's URL rewriting issue\n";
echo "4. If nothing works, it's a fundamental server/Laravel issue\n";

echo "\n💡 QUICK WINS TO TRY:\n";
echo "A. Clear everything:\n";
echo "   composer dump-autoload --optimize\n";
echo "   php artisan optimize:clear\n";
echo "\nB. Test built-in server:\n";
echo "   php artisan serve --host=0.0.0.0 --port=8001\n";
echo "   Access: http://8.215.70.68:8001/admin\n";

?>
