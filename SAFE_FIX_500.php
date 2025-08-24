<?php
/**
 * SAFE FIX FOR 500 ERROR - Solusi Aman untuk Error 500
 * Upload dan jalankan untuk memperbaiki error 500
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔧 SAFE FIX FOR 500 ERROR - Starting...\n\n";

// 1. BACKUP EXISTING FILES
echo "💾 Step 1: Creating backups...\n";
$backupDir = 'backup_' . date('Y-m-d_H-i-s');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$filesToBackup = [
    'app/Providers/AppServiceProvider.php',
    '.env',
    'bootstrap/cache/packages.php'
];

foreach ($filesToBackup as $file) {
    if (file_exists($file)) {
        $backupPath = $backupDir . '/' . basename($file);
        copy($file, $backupPath);
        echo "✅ Backed up: $file to $backupPath\n";
    }
}

// 2. CREATE MINIMAL .ENV
echo "\n⚙️ Step 2: Creating minimal .env...\n";
if (!file_exists('.env')) {
    $minimalEnv = 'APP_NAME="Laravel"
APP_ENV=production
APP_KEY=base64:' . base64_encode(random_bytes(32)) . '
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

LOG_CHANNEL=single
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error';

    file_put_contents('.env', $minimalEnv);
    echo "✅ Created minimal .env file\n";
} else {
    echo "✅ .env file already exists\n";
}

// 3. CREATE SUPER MINIMAL APPSERVICEPROVIDER
echo "\n🔧 Step 3: Creating minimal AppServiceProvider...\n";
$minimalAppServiceProvider = '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Minimal registration - no Pail or other dev packages
    }

    public function boot(): void
    {
        // Minimal boot - no dev-specific code
    }
}';

file_put_contents('app/Providers/AppServiceProvider.php', $minimalAppServiceProvider);
echo "✅ Created minimal AppServiceProvider\n";

// 4. CLEAR ALL CACHES SAFELY
echo "\n🗑️ Step 4: Clearing caches safely...\n";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/compiled.php'
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "✅ Deleted: $file\n";
    }
}

// 5. CREATE SAFE PACKAGES.PHP
echo "\n📦 Step 5: Creating safe packages.php...\n";
$safePackages = '<?php
// Safe packages configuration for production
return [
    "providers" => [
        // Core Laravel providers only
    ],
    "eager" => [],
    "deferred" => [],
    "when" => []
];';

if (!is_dir('bootstrap/cache')) {
    mkdir('bootstrap/cache', 0755, true);
}
file_put_contents('bootstrap/cache/packages.php', $safePackages);
echo "✅ Created safe packages.php\n";

// 6. CHECK AND CREATE STORAGE DIRECTORIES
echo "\n📁 Step 6: Ensuring storage directories exist...\n";
$storageDirs = [
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs'
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created: $dir\n";
    } else {
        echo "✅ Exists: $dir\n";
    }
}

// 7. CREATE SIMPLE INDEX.PHP IF MISSING
echo "\n📄 Step 7: Checking index.php...\n";
if (!file_exists('index.php')) {
    $simpleIndex = '<?php
// Simple Laravel bootstrap
define("LARAVEL_START", microtime(true));

// Check for maintenance mode
if (file_exists($maintenance = __DIR__."/storage/framework/maintenance.php")) {
    require $maintenance;
}

// Load autoloader
if (!file_exists(__DIR__."/vendor/autoload.php")) {
    die("Vendor autoload not found. Please run: composer install");
}
require __DIR__."/vendor/autoload.php";

// Bootstrap Laravel
try {
    $app = require_once __DIR__."/bootstrap/app.php";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    $response->send();
    
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    http_response_code(500);
    echo "Application Error: " . $e->getMessage();
    error_log("Laravel Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
}';

    file_put_contents('index.php', $simpleIndex);
    echo "✅ Created safe index.php\n";
} else {
    echo "✅ index.php already exists\n";
}

// 8. CREATE SIMPLE TEST FILE
echo "\n🧪 Step 8: Creating test file...\n";
$testFile = '<?php
// Simple test to check if basic PHP works
echo "<h1>✅ PHP Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Time: " . date("Y-m-d H:i:s") . "</p>";

// Test if Laravel files exist
echo "<h2>Laravel Files Check:</h2>";
$files = [
    "vendor/autoload.php",
    "bootstrap/app.php", 
    ".env",
    "app/Providers/AppServiceProvider.php"
];

foreach ($files as $file) {
    echo $file . ": " . (file_exists($file) ? "✅ EXISTS" : "❌ MISSING") . "<br>";
}

echo "<hr>";
echo "<a href=\"/\">Try Main Site</a> | ";
echo "<a href=\"/admin\">Try Admin</a>";
';

file_put_contents('test-safe.php', $testFile);
echo "✅ Created test-safe.php\n";

// 9. SET BASIC PERMISSIONS
if (PHP_OS_FAMILY !== 'Windows') {
    echo "\n🔐 Step 9: Setting basic permissions...\n";
    chmod('storage', 0755);
    chmod('bootstrap/cache', 0755);
    echo "✅ Set basic permissions\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 SAFE FIX COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "📋 What was done:\n";
echo "   - Created backups in: $backupDir\n";
echo "   - Minimal .env configuration\n";
echo "   - Super minimal AppServiceProvider\n";
echo "   - Cleared all caches\n";
echo "   - Safe packages.php\n";
echo "   - Ensured storage directories\n";
echo "   - Safe index.php with error handling\n\n";

echo "🧪 Test steps:\n";
echo "   1. First test: your-domain.com/test-safe.php\n";
echo "   2. If that works, try: your-domain.com/\n";
echo "   3. Check for any error messages\n\n";

echo "🚨 If still getting 500 error:\n";
echo "   1. Run: your-domain.com/diagnostic.php\n";
echo "   2. Check server error logs\n";
echo "   3. Contact hosting support\n\n";

echo "📁 Backups created in: $backupDir\n";
echo "🗑️ Delete these test files after success:\n";
echo "   - SAFE_FIX_500.php\n";
echo "   - test-safe.php\n";
echo "   - diagnostic.php\n\n";
?>
