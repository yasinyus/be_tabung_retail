<?php
// TEST PERMISSIONS - Upload sebagai test-permissions.php
echo "<h1>🔐 PERMISSION TEST</h1>";

echo "<h2>📁 Directory Permissions</h2>";
$dirs = [
    'storage',
    'storage/app',
    'storage/framework', 
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
    'app',
    'config'
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir) ? "✅ Writable" : "❌ Not Writable";
        $readable = is_readable($dir) ? "✅ Readable" : "❌ Not Readable";
        echo "$dir: <strong>$perms</strong> - $writable - $readable<br>";
    } else {
        echo "$dir: ❌ <strong>NOT FOUND</strong><br>";
    }
}

echo "<h2>📄 File Permissions</h2>";
$files = [
    '.env',
    'index.php',
    '.htaccess',
    'composer.json'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        $readable = is_readable($file) ? "✅ Readable" : "❌ Not Readable";
        echo "$file: <strong>$perms</strong> - $readable<br>";
    } else {
        echo "$file: ❌ <strong>NOT FOUND</strong><br>";
    }
}

echo "<h2>✍️ Write Test</h2>";
$testFile = 'storage/test-write-' . time() . '.txt';
if (file_put_contents($testFile, 'Permission test: ' . date('Y-m-d H:i:s'))) {
    echo "✅ <strong>CAN WRITE</strong> to storage/<br>";
    unlink($testFile);
} else {
    echo "❌ <strong>CANNOT WRITE</strong> to storage/<br>";
}

$testCache = 'bootstrap/cache/test-' . time() . '.txt';
if (file_put_contents($testCache, 'Cache test')) {
    echo "✅ <strong>CAN WRITE</strong> to bootstrap/cache/<br>";
    unlink($testCache);
} else {
    echo "❌ <strong>CANNOT WRITE</strong> to bootstrap/cache/<br>";
}

echo "<h2>🔍 Server Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current User: " . get_current_user() . "<br>";

echo "<hr>";
echo "<h3>🔧 If Problems Found:</h3>";
echo "<p>Contact hosting support and ask them to set:</p>";
echo "<ul>";
echo "<li><strong>Folders to 755:</strong> storage, bootstrap/cache, storage/framework</li>";
echo "<li><strong>Files to 644:</strong> .env, index.php, .htaccess</li>";
echo "<li><strong>Make writable:</strong> storage and all subfolders</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>🔗 Next Tests:</h3>";
echo '<a href="/test-laravel.php">Test Laravel Bootstrap</a><br>';
echo '<a href="/">Test Main Site</a><br>';
echo '<a href="/admin">Test Admin (After Fix)</a>';
?>
