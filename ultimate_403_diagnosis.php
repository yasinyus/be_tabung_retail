<?php

echo "=== ULTIMATE 403 DIAGNOSIS ===\n\n";

// Test 1: Basic PHP and file access
echo "1️⃣  Basic PHP Test...\n";
echo "   PHP Version: " . phpversion() . "\n";
echo "   Current Directory: " . __DIR__ . "\n";
echo "   Script running: " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "\n";

// Test 2: File structure check
echo "\n2️⃣  Critical Files Check...\n";
$criticalFiles = [
    'vendor/autoload.php',
    'bootstrap/app.php', 
    'public/index.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Models/User.php',
    '.env'
];

foreach ($criticalFiles as $file) {
    $exists = file_exists($file);
    $readable = $exists ? is_readable($file) : false;
    echo "   " . ($exists ? '✅' : '❌') . " {$file}" . ($readable ? '' : ' (NOT READABLE)') . "\n";
}

// Test 3: Try to bootstrap Laravel
echo "\n3️⃣  Laravel Bootstrap Test...\n";
try {
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        echo "   ✅ Autoloader loaded\n";
        
        if (file_exists('bootstrap/app.php')) {
            $app = require_once 'bootstrap/app.php';
            echo "   ✅ App bootstrapped\n";
            
            try {
                $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
                $kernel->bootstrap();
                echo "   ✅ Kernel bootstrapped\n";
                
                // Test config loading
                $appName = config('app.name', 'FAILED');
                echo "   ✅ Config loaded: {$appName}\n";
                
                // Test database
                try {
                    $users = \App\Models\User::count();
                    echo "   ✅ Database working: {$users} users\n";
                } catch (Exception $e) {
                    echo "   ❌ Database failed: {$e->getMessage()}\n";
                }
                
                // Test Filament
                try {
                    $panels = \Filament\Facades\Filament::getPanels();
                    echo "   ✅ Filament panels: " . count($panels) . "\n";
                } catch (Exception $e) {
                    echo "   ❌ Filament failed: {$e->getMessage()}\n";
                }
                
                // Test routes
                try {
                    $routes = \Illuminate\Support\Facades\Route::getRoutes();
                    $adminRoutes = 0;
                    foreach ($routes as $route) {
                        if (str_contains($route->uri(), 'admin')) {
                            $adminRoutes++;
                        }
                    }
                    echo "   ✅ Routes working: {$adminRoutes} admin routes\n";
                } catch (Exception $e) {
                    echo "   ❌ Routes failed: {$e->getMessage()}\n";
                }
                
            } catch (Exception $e) {
                echo "   ❌ Kernel bootstrap failed: {$e->getMessage()}\n";
            }
        } else {
            echo "   ❌ bootstrap/app.php not found\n";
        }
    } else {
        echo "   ❌ vendor/autoload.php not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Bootstrap completely failed: {$e->getMessage()}\n";
}

// Test 4: Web server detection
echo "\n4️⃣  Web Server Analysis...\n";
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
echo "   Server: {$serverSoftware}\n";

if (strpos($serverSoftware, 'Apache') !== false) {
    echo "   Detected: Apache Server\n";
    echo "   Check: mod_rewrite enabled?\n";
} elseif (strpos($serverSoftware, 'nginx') !== false) {
    echo "   Detected: Nginx Server\n";
    echo "   Check: try_files configuration?\n";
} else {
    echo "   Unknown server type\n";
}

// Test 5: .htaccess analysis
echo "\n5️⃣  .htaccess Deep Analysis...\n";
$htaccessPath = 'public/.htaccess';
if (file_exists($htaccessPath)) {
    $htaccess = file_get_contents($htaccessPath);
    echo "   ✅ .htaccess exists (" . strlen($htaccess) . " bytes)\n";
    
    // Check for problematic rules
    $rules = [
        'RewriteEngine On' => 'RewriteEngine On',
        'Front Controller' => 'RewriteRule \^ index\.php',
        'Authorization Header' => 'HTTP_AUTHORIZATION',
        'Trailing Slash' => 'RewriteCond.*REQUEST_URI.*\(\.\+\)'
    ];
    
    foreach ($rules as $name => $pattern) {
        $found = preg_match('/' . str_replace(['\\', '^', '.'], ['\\\\', '\^', '\.'], $pattern) . '/i', $htaccess);
        echo "   " . ($found ? '✅' : '❌') . " {$name}\n";
    }
} else {
    echo "   ❌ .htaccess not found\n";
}

echo "\n🔧 COMPREHENSIVE FIX STRATEGY:\n";
echo "\nIF Laravel bootstrap is working:\n";
echo "1. The issue is web server configuration\n";
echo "2. Try: http://your-domain/index.php/admin\n";
echo "3. Check Apache mod_rewrite or Nginx try_files\n";

echo "\nIF Laravel bootstrap is NOT working:\n";
echo "1. Run: composer install --optimize-autoloader\n";
echo "2. Run: php artisan key:generate\n";
echo "3. Check .env file settings\n";

echo "\nIF everything looks OK but still 403:\n";
echo "1. Server-level permission issue\n";
echo "2. SELinux/AppArmor blocking access\n";
echo "3. Firewall or security software\n";

echo "\n🚀 NEXT ACTIONS:\n";
echo "A. Test built-in server: php artisan serve --host=0.0.0.0 --port=8080\n";
echo "B. Test direct index.php: http://your-domain/index.php/admin\n";
echo "C. Check server logs: tail -f /var/log/apache2/error.log\n";

?>
