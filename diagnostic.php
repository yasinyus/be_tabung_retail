<?php
/**
 * DIAGNOSTIC SCRIPT - Untuk melihat apa yang salah
 * Upload dan jalankan untuk debug error 500
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 DIAGNOSTIC SCRIPT - Debug Error 500</h1>";
echo "<hr>";

// 1. Check PHP Info
echo "<h2>📋 PHP Information</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PHP OS: " . PHP_OS . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Path: " . __DIR__ . "<br>";
echo "<hr>";

// 2. Check Laravel Files
echo "<h2>📁 Laravel Files Check</h2>";
$requiredFiles = [
    'vendor/autoload.php',
    'bootstrap/app.php',
    '.env',
    'app/Providers/AppServiceProvider.php',
    'config/app.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ EXISTS: $file<br>";
    } else {
        echo "❌ MISSING: $file<br>";
    }
}
echo "<hr>";

// 3. Check Permissions
echo "<h2>🔐 Permissions Check</h2>";
$checkDirs = [
    'storage',
    'storage/logs',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache'
];

foreach ($checkDirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "📁 $dir: $perms<br>";
    } else {
        echo "❌ MISSING DIR: $dir<br>";
    }
}
echo "<hr>";

// 4. Test Basic Laravel Bootstrap
echo "<h2>🚀 Laravel Bootstrap Test</h2>";
try {
    if (file_exists('vendor/autoload.php')) {
        require 'vendor/autoload.php';
        echo "✅ Autoloader loaded successfully<br>";
        
        if (file_exists('bootstrap/app.php')) {
            $app = require 'bootstrap/app.php';
            echo "✅ Laravel app bootstrapped successfully<br>";
        } else {
            echo "❌ bootstrap/app.php not found<br>";
        }
    } else {
        echo "❌ vendor/autoload.php not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Bootstrap Error: " . $e->getMessage() . "<br>";
    echo "Error File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}
echo "<hr>";

// 5. Check .env file
echo "<h2>⚙️ Environment Check</h2>";
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    echo "✅ .env file exists<br>";
    echo "APP_ENV: " . (getenv('APP_ENV') ?: 'Not set') . "<br>";
    echo "APP_DEBUG: " . (getenv('APP_DEBUG') ?: 'Not set') . "<br>";
} else {
    echo "❌ .env file missing<br>";
}
echo "<hr>";

// 6. Check Laravel Log
echo "<h2>📋 Recent Laravel Logs</h2>";
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $recentLogs = substr($logs, -2000); // Last 2000 characters
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;'>";
    echo htmlspecialchars($recentLogs);
    echo "</pre>";
} else {
    echo "❌ No log file found<br>";
}
echo "<hr>";

// 7. Check Web Server Error Log
echo "<h2>🌐 Server Error Check</h2>";
if (function_exists('error_get_last')) {
    $lastError = error_get_last();
    if ($lastError) {
        echo "<pre style='background: #ffe6e6; padding: 10px;'>";
        print_r($lastError);
        echo "</pre>";
    } else {
        echo "✅ No recent PHP errors<br>";
    }
}

// 8. Suggestions
echo "<h2>💡 Suggestions</h2>";
echo "<ul>";
echo "<li>If vendor/autoload.php missing: Run 'composer install'</li>";
echo "<li>If permissions issues: Set directories to 755, files to 644</li>";
echo "<li>If .env missing: Copy from .env.example</li>";
echo "<li>Check storage/logs/laravel.log for detailed errors</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
