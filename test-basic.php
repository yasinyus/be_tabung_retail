<?php
// TEST BASIC - Upload sebagai test-basic.php
echo "<h1>🧪 BASIC TEST</h1>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Path:</strong> " . __DIR__ . "</p>";

echo "<h2>📁 File Check</h2>";
$files = [
    'vendor/autoload.php',
    'bootstrap/app.php',
    '.env',
    'index.php',
    '.htaccess'
];

foreach ($files as $file) {
    $status = file_exists($file) ? "✅ EXISTS" : "❌ MISSING";
    echo "$file: $status<br>";
}

echo "<h2>📁 Directory Check</h2>";
$dirs = [
    'storage',
    'storage/logs', 
    'bootstrap/cache',
    'app'
];

foreach ($dirs as $dir) {
    $status = is_dir($dir) ? "✅ EXISTS" : "❌ MISSING";
    echo "$dir: $status<br>";
}

echo "<hr>";
echo "<h3>🔗 Next Tests:</h3>";
echo '<a href="/test-laravel.php">Test Laravel Bootstrap</a><br>';
echo '<a href="/">Test Main Site</a><br>';
echo '<a href="/admin">Test Admin Panel</a>';
?>
