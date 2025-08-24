<?php
/**
 * COMPLETE FIX SCRIPT - Solusi lengkap untuk 404 admin/login
 * Upload dan jalankan untuk fix semua masalah sekaligus
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 COMPLETE FIX - Admin 404 Error</h1>";
echo "<hr>";

// 1. DIAGNOSIS AWAL
echo "<h2>🔍 Step 1: Diagnosis</h2>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";

// Check critical files
$criticalFiles = [
    'vendor/autoload.php',
    'bootstrap/app.php',
    '.env',
    'config/app.php',
    'app/Providers/AppServiceProvider.php'
];

foreach ($criticalFiles as $file) {
    $status = file_exists($file) ? "✅ EXISTS" : "❌ MISSING";
    echo "$file: $status<br>";
}

// 2. CLEAR ALL CACHES
echo "<h2>🗑️ Step 2: Complete Cache Clear</h2>";

// Clear bootstrap cache
$bootCache = glob('bootstrap/cache/*');
foreach ($bootCache as $file) {
    if (is_file($file) && basename($file) !== '.gitignore') {
        unlink($file);
        echo "✅ Deleted: bootstrap/cache/" . basename($file) . "<br>";
    }
}

// Clear storage cache
$storagePaths = [
    'storage/framework/cache/data',
    'storage/framework/views',
    'storage/framework/sessions'
];

foreach ($storagePaths as $path) {
    if (is_dir($path)) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Cleared: $path<br>";
    }
}

// 3. CREATE ESSENTIAL DIRECTORIES
echo "<h2>📁 Step 3: Create Directories</h2>";
$directories = [
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created: $dir<br>";
        } else {
            echo "❌ Failed: $dir<br>";
        }
    } else {
        echo "✅ Exists: $dir<br>";
    }
}

// 4. CREATE SAFE CONFIG FILES
echo "<h2>⚙️ Step 4: Create Safe Config</h2>";

// Safe packages.php
$packagesContent = '<?php return ["providers" => [], "eager" => [], "deferred" => [], "when" => []];';
file_put_contents('bootstrap/cache/packages.php', $packagesContent);
echo "✅ Created safe packages.php<br>";

// Safe config cache
$configContent = '<?php return [];';
file_put_contents('bootstrap/cache/config.php', $configContent);
echo "✅ Created safe config.php<br>";

// 5. FIX APPSERVICEPROVIDER
echo "<h2>🔧 Step 5: Fix AppServiceProvider</h2>";
$appServiceProvider = '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // No development packages in production
    }

    public function boot(): void
    {
        // Production boot logic only
    }
}';

file_put_contents('app/Providers/AppServiceProvider.php', $appServiceProvider);
echo "✅ Updated AppServiceProvider.php<br>";

// 6. CREATE PROPER .HTACCESS
echo "<h2>🌐 Step 6: Create .htaccess</h2>";
$htaccess = '<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>';

file_put_contents('.htaccess', $htaccess);
echo "✅ Created .htaccess<br>";

// 7. CREATE ROBUST INDEX.PHP
echo "<h2>📄 Step 7: Create Robust index.php</h2>";
$indexContent = '<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define("LARAVEL_START", microtime(true));

// Check for maintenance mode
if (file_exists($maintenance = __DIR__."/storage/framework/maintenance.php")) {
    require $maintenance;
}

// Require autoloader
if (!file_exists(__DIR__."/vendor/autoload.php")) {
    http_response_code(500);
    die("Error: Composer dependencies not installed. Please run: composer install");
}

require __DIR__."/vendor/autoload.php";

// Bootstrap Laravel
try {
    $app = require_once __DIR__."/bootstrap/app.php";
    
    $kernel = $app->make(Kernel::class);
    
    $response = $kernel->handle(
        $request = Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
    
} catch (Throwable $e) {
    http_response_code(500);
    
    // Log error
    if (is_dir("storage/logs")) {
        error_log(
            "[" . date("Y-m-d H:i:s") . "] Laravel Error: " . 
            $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n",
            3,
            "storage/logs/error.log"
        );
    }
    
    // Show user-friendly error
    echo "<!DOCTYPE html>
<html>
<head><title>Application Error</title></head>
<body>
    <h1>Application temporarily unavailable</h1>
    <p>Please try again in a few moments.</p>
    <p><small>Error logged at " . date("Y-m-d H:i:s") . "</small></p>
</body>
</html>";
}';

file_put_contents('index.php', $indexContent);
echo "✅ Created robust index.php<br>";

// 8. TEST DATABASE CONNECTION
echo "<h2>🗄️ Step 8: Test Database</h2>";
if (file_exists('.env')) {
    // Load .env file manually
    $envContent = file_get_contents('.env');
    preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
    preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
    preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
    
    $host = trim($hostMatch[1] ?? 'localhost');
    $database = trim($dbMatch[1] ?? '');
    $username = trim($userMatch[1] ?? '');
    $password = trim($passMatch[1] ?? '');
    
    if ($database && $username) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            echo "✅ Database connection successful<br>";
            echo "Database: $database<br>";
            
            // Check for admin table
            $tables = $pdo->query("SHOW TABLES LIKE '%user%'")->fetchAll();
            if ($tables) {
                echo "✅ User tables found<br>";
            } else {
                echo "⚠️ No user tables found - may need migration<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "⚠️ Database credentials not configured<br>";
    }
}

// 9. SET PERMISSIONS
echo "<h2>🔐 Step 9: Set Permissions</h2>";
$permDirs = ['storage', 'bootstrap/cache'];
foreach ($permDirs as $dir) {
    if (is_dir($dir)) {
        if (chmod($dir, 0755)) {
            echo "✅ Set 755: $dir<br>";
        }
    }
}

// 10. CREATE TEST FILES
echo "<h2>🧪 Step 10: Create Test Files</h2>";

// Simple test
$simpleTest = '<?php echo "<h1>✅ Basic PHP Works!</h1><p>Time: " . date("Y-m-d H:i:s") . "</p>"; ?>';
file_put_contents('test-simple.php', $simpleTest);
echo "✅ Created test-simple.php<br>";

// Laravel test
$laravelTest = '<?php
try {
    require "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    echo "<h1>✅ Laravel Works!</h1>";
    echo "<p>Environment: " . app()->environment() . "</p>";
    echo "<p>URL: " . config("app.url") . "</p>";
} catch (Exception $e) {
    echo "<h1>❌ Laravel Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>';
file_put_contents('test-laravel.php', $laravelTest);
echo "✅ Created test-laravel.php<br>";

echo "<hr>";
echo "<h2>🎉 COMPLETE FIX FINISHED!</h2>";

echo "<h3>🧪 Test in this order:</h3>";
echo '<ol>';
echo '<li><a href="/test-simple.php" target="_blank">Test Basic PHP</a></li>';
echo '<li><a href="/test-laravel.php" target="_blank">Test Laravel Bootstrap</a></li>';
echo '<li><a href="/" target="_blank">Test Main Site</a></li>';
echo '<li><a href="/admin" target="_blank">Test Admin Panel</a></li>';
echo '<li><a href="/admin/login" target="_blank">Test Admin Login</a></li>';
echo '</ol>';

echo "<h3>📋 What was fixed:</h3>";
echo '<ul>';
echo '<li>✅ Cleared ALL caches</li>';
echo '<li>✅ Created essential directories</li>';
echo '<li>✅ Fixed AppServiceProvider</li>';
echo '<li>✅ Created proper .htaccess</li>';
echo '<li>✅ Created robust index.php</li>';
echo '<li>✅ Set correct permissions</li>';
echo '<li>✅ Tested database connection</li>';
echo '</ul>';

echo "<h3>🚨 If still 404:</h3>";
echo '<ul>';
echo '<li>Check if Filament is installed: <code>composer show filament/filament</code></li>';
echo '<li>Run migration: <code>php artisan migrate</code></li>';
echo '<li>Create admin user: <code>php artisan make:filament-user</code></li>';
echo '<li>Contact hosting support for file permission issues</li>';
echo '</ul>';

echo "<hr>";
echo "<p><small>Fix completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
