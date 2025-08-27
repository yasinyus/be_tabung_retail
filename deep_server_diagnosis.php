<?php

echo "=== DEEP SERVER DIAGNOSIS ===\n\n";

// Test direct route access
echo "1️⃣  Testing Direct Route Access...\n";

try {
    // Bootstrap Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    // Test route registration
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = [];
    $loginRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        $methods = implode(',', $route->methods());
        $middleware = implode(',', $route->middleware());
        
        if (str_contains($uri, 'admin')) {
            $adminRoutes[] = [
                'uri' => $uri,
                'methods' => $methods,
                'middleware' => $middleware,
                'name' => $route->getName()
            ];
        }
        
        if (str_contains($uri, 'login')) {
            $loginRoutes[] = $uri;
        }
    }
    
    echo "   Total routes: " . count($routes) . "\n";
    echo "   Admin routes: " . count($adminRoutes) . "\n";
    echo "   Login routes: " . count($loginRoutes) . "\n\n";
    
    echo "   Admin routes detail:\n";
    foreach (array_slice($adminRoutes, 0, 10) as $route) {
        echo "     - {$route['uri']} [{$route['methods']}] - MW: {$route['middleware']}\n";
    }
    
    // Test Filament panels
    echo "\n2️⃣  Testing Filament Panels...\n";
    try {
        $panels = \Filament\Facades\Filament::getPanels();
        foreach ($panels as $panelId => $panel) {
            echo "   Panel: {$panelId}\n";
            echo "     Path: " . $panel->getPath() . "\n";
            echo "     ID: " . $panel->getId() . "\n";
            
            if (method_exists($panel, 'hasLogin')) {
                echo "     Has login: " . ($panel->hasLogin() ? 'YES' : 'NO') . "\n";
            }
            
            if (method_exists($panel, 'getAuthGuard')) {
                echo "     Auth guard: " . $panel->getAuthGuard() . "\n";
            }
        }
    } catch (Exception $e) {
        echo "   ❌ Filament panels error: {$e->getMessage()}\n";
    }
    
    // Test direct middleware
    echo "\n3️⃣  Testing Middleware Stack...\n";
    try {
        // Find admin route
        $adminRoute = null;
        foreach ($routes as $route) {
            if ($route->uri() === 'admin') {
                $adminRoute = $route;
                break;
            }
        }
        
        if ($adminRoute) {
            echo "   Admin route found: " . $adminRoute->uri() . "\n";
            echo "   Middleware: " . implode(', ', $adminRoute->middleware()) . "\n";
            echo "   Action: " . $adminRoute->getActionName() . "\n";
        } else {
            echo "   ❌ No exact 'admin' route found\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Middleware test error: {$e->getMessage()}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Laravel bootstrap failed: {$e->getMessage()}\n";
}

// Test server-level restrictions
echo "\n4️⃣  Server-Level Checks...\n";

// Check if we can determine web server
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
echo "   Server software: {$serverSoftware}\n";

// Check request environment
echo "   Request method: " . ($_SERVER['REQUEST_METHOD'] ?? 'Unknown') . "\n";
echo "   Script name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "\n";
echo "   Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";

// Test file access
echo "\n5️⃣  File Access Tests...\n";
$testFiles = [
    'public/index.php' => 'Main entry point',
    'public/.htaccess' => 'URL rewriting rules',
    'app/Providers/Filament/AdminPanelProvider.php' => 'Filament provider',
    'vendor/filament/filament/src/Http/Controllers/Auth/LoginController.php' => 'Filament login controller'
];

foreach ($testFiles as $file => $description) {
    $exists = file_exists($file);
    $readable = $exists ? is_readable($file) : false;
    $status = $exists ? ($readable ? '✅' : '⚠️ EXISTS but NOT READABLE') : '❌ NOT FOUND';
    echo "   {$status} {$description}: {$file}\n";
}

echo "\n6️⃣  Permission Analysis...\n";
$dirs = ['public', 'storage', 'bootstrap/cache', 'app'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($dir))['name'] ?? 'unknown' : 'unknown';
        echo "   {$dir}: {$perms} (owner: {$owner})\n";
    }
}

echo "\n🔧 SPECIFIC SOLUTIONS TO TRY:\n";
echo "\nA. BUILT-IN SERVER TEST (bypasses web server completely):\n";
echo "   php artisan serve --host=0.0.0.0 --port=8080\n";
echo "   Then test: http://8.215.70.68:8080/admin\n";

echo "\nB. WEB SERVER RESTART:\n";
echo "   sudo systemctl restart apache2\n";
echo "   sudo systemctl restart nginx\n";

echo "\nC. CHECK LOGS:\n";
echo "   tail -f /var/log/apache2/error.log\n";
echo "   tail -f /var/log/nginx/error.log\n";
echo "   tail -f storage/logs/laravel.log\n";

echo "\nD. PERMISSION FIX:\n";
echo "   sudo chown -R www-data:www-data .\n";
echo "   sudo chmod -R 755 public\n";
echo "   sudo chmod -R 775 storage bootstrap/cache\n";

echo "\n🚨 IF BUILT-IN SERVER WORKS:\n";
echo "The issue is definitely web server configuration!\n";
echo "Check virtual host, .htaccess processing, mod_rewrite, etc.\n";

?>
