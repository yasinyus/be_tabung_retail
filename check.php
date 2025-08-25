<?php
echo "<h1>🔍 BASIC FILE CHECK</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

echo "<h2>📁 Critical Files:</h2>";
$files = [
    'vendor/autoload.php',
    'bootstrap/app.php', 
    '.env',
    'config/app.php',
    'index.php',
    '.htaccess'
];

foreach ($files as $file) {
    $exists = file_exists($file);
    $status = $exists ? "✅ ADA" : "❌ TIDAK ADA";
    echo "$file: <strong>$status</strong><br>";
}

echo "<h2>🚀 Autoload Test:</h2>";
if (file_exists('vendor/autoload.php')) {
    try {
        require 'vendor/autoload.php';
        echo "✅ <strong>AUTOLOAD BERHASIL</strong><br>";
        
        if (file_exists('bootstrap/app.php')) {
            echo "Testing Laravel bootstrap...<br>";
            $app = require 'bootstrap/app.php';
            echo "✅ <strong>LARAVEL BOOTSTRAP BERHASIL</strong><br>";
        }
        
    } catch (Exception $e) {
        echo "❌ <strong>AUTOLOAD ERROR:</strong> " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ <strong>vendor/autoload.php TIDAK ADA</strong><br>";
    echo "Perlu jalankan: <code>composer install</code><br>";
}

echo "<h2>📁 Directory Check:</h2>";
$dirs = [
    'storage',
    'storage/logs',
    'bootstrap/cache',
    'vendor'
];

foreach ($dirs as $dir) {
    $exists = is_dir($dir);
    $status = $exists ? "✅ ADA" : "❌ TIDAK ADA";
    echo "$dir: <strong>$status</strong><br>";
}

echo "<hr>";
echo "<h3>🎯 Next Steps:</h3>";
if (file_exists('vendor/autoload.php')) {
    echo "<p>✅ Files OK. <a href='/'>Test Main Site</a></p>";
} else {
    echo "<p>❌ Need composer install first!</p>";
}
?>
