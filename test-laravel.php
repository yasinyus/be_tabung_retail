<?php
// TEST LARAVEL - Upload sebagai test-laravel.php
echo "<h1>🚀 LARAVEL BOOTSTRAP TEST</h1>";

try {
    echo "<p>Testing autoloader...</p>";
    if (!file_exists('vendor/autoload.php')) {
        die("❌ vendor/autoload.php NOT FOUND - Need composer install");
    }
    require 'vendor/autoload.php';
    echo "✅ Autoloader loaded<br>";
    
    echo "<p>Testing Laravel bootstrap...</p>";
    if (!file_exists('bootstrap/app.php')) {
        die("❌ bootstrap/app.php NOT FOUND");
    }
    $app = require_once 'bootstrap/app.php';
    echo "✅ Laravel app bootstrapped<br>";
    
    echo "<p>Testing environment...</p>";
    echo "APP_ENV: " . env('APP_ENV', 'not set') . "<br>";
    echo "APP_URL: " . env('APP_URL', 'not set') . "<br>";
    echo "DB_CONNECTION: " . env('DB_CONNECTION', 'not set') . "<br>";
    
    echo "<h2>✅ LARAVEL BOOTSTRAP SUCCESS!</h2>";
    echo "<p>Laravel is working properly!</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ LARAVEL BOOTSTRAP FAILED!</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<h3>🔗 Next Tests:</h3>";
echo '<a href="/">Test Main Site</a><br>';
echo '<a href="/admin">Test Admin Panel</a>';
?>
