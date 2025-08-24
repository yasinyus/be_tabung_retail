<?php
// TEST FILE - Copy paste ini jadi file test.php di hosting
echo "<h1>🧪 Test Laravel Status</h1>";
echo "<hr>";

echo "<h2>📋 Basic Check</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Path: " . __DIR__ . "<br>";
echo "Time: " . date('Y-m-d H:i:s') . "<br>";

echo "<h2>📁 File Check</h2>";
$files = [
    'vendor/autoload.php',
    'bootstrap/app.php',
    '.env',
    'app/Providers/AppServiceProvider.php',
    'index.php'
];

foreach ($files as $file) {
    $status = file_exists($file) ? "✅ EXISTS" : "❌ MISSING";
    echo "$file: $status<br>";
}

echo "<h2>📁 Directory Check</h2>";
$dirs = [
    'storage',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($dirs as $dir) {
    $status = is_dir($dir) ? "✅ EXISTS" : "❌ MISSING";
    echo "$dir: $status<br>";
}

if (file_exists('vendor/autoload.php')) {
    echo "<h2>🚀 Laravel Test</h2>";
    try {
        require 'vendor/autoload.php';
        echo "✅ Autoloader OK<br>";
        
        if (file_exists('bootstrap/app.php')) {
            $app = require 'bootstrap/app.php';
            echo "✅ Laravel Bootstrap OK<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
}

echo "<hr>";
echo "<h3>🔗 Test Links:</h3>";
echo '<a href="/">Main Site</a> | ';
echo '<a href="/admin">Admin Panel</a>';
?>
