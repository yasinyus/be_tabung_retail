<?php
// QUICK FIX 403 - Upload sebagai fix-403.php
echo "<h1>🔧 QUICK FIX 403 FORBIDDEN</h1>";

// 1. Clear all caches
echo "<h2>🗑️ Step 1: Clearing Caches</h2>";
$cacheFiles = glob('bootstrap/cache/*');
foreach ($cacheFiles as $file) {
    if (is_file($file)) {
        unlink($file);
        echo "✅ Deleted: " . basename($file) . "<br>";
    }
}

$storageCaches = [
    'storage/framework/cache/data',
    'storage/framework/views',
    'storage/framework/sessions'
];

foreach ($storageCaches as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Cleared: $dir<br>";
    }
}

// 2. Create necessary directories
echo "<h2>📁 Step 2: Creating Directories</h2>";
$dirs = [
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created: $dir<br>";
        } else {
            echo "❌ Failed to create: $dir<br>";
        }
    } else {
        echo "✅ Exists: $dir<br>";
    }
}

// 3. Set permissions
echo "<h2>🔐 Step 3: Setting Permissions</h2>";
$permDirs = [
    'storage',
    'storage/framework',
    'storage/logs', 
    'bootstrap/cache'
];

foreach ($permDirs as $dir) {
    if (is_dir($dir)) {
        if (chmod($dir, 0755)) {
            echo "✅ Set 755: $dir<br>";
        } else {
            echo "❌ Failed to chmod: $dir<br>";
        }
    }
}

// 4. Create safe packages.php
echo "<h2>📦 Step 4: Creating Safe Packages</h2>";
$packagesContent = '<?php return ["providers" => [], "eager" => [], "deferred" => [], "when" => []];';
if (file_put_contents('bootstrap/cache/packages.php', $packagesContent)) {
    echo "✅ Created safe packages.php<br>";
} else {
    echo "❌ Failed to create packages.php<br>";
}

// 5. Test write permissions
echo "<h2>✍️ Step 5: Testing Write Access</h2>";
$testFile = 'storage/test-' . time() . '.txt';
if (file_put_contents($testFile, 'Test write: ' . date('Y-m-d H:i:s'))) {
    echo "✅ Storage is writable<br>";
    unlink($testFile);
} else {
    echo "❌ Storage is NOT writable<br>";
}

echo "<hr>";
echo "<h2>🎉 FIX COMPLETED!</h2>";
echo "<h3>🧪 Test Now:</h3>";
echo '<p><a href="/" target="_blank">🏠 Test Main Site</a></p>';
echo '<p><a href="/admin" target="_blank">🔐 Test Admin Panel</a></p>';

echo "<hr>";
echo "<h3>🚨 If Still 403:</h3>";
echo "<ul>";
echo "<li>Contact hosting support for permission fix</li>";
echo "<li>Check database connection (.env file)</li>";
echo "<li>Ensure Filament is properly installed</li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Time: " . date('Y-m-d H:i:s') . "</small></p>";
?>
