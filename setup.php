<?php
// setup.php - Manual setup untuk QR Code system

echo "🔧 Starting manual setup...\n";

// 1. Create storage directories
$directories = [
    'storage/app/public',
    'storage/app/public/qr_codes',
    'storage/app/public/qr_codes/tabung',
    'storage/app/public/qr_codes/armada',
    'storage/app/public/qr_codes/gudang',
    'storage/app/public/qr_codes/pelanggan'
];

echo "📁 Creating directories...\n";
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "Created: $dir\n";
    } else {
        echo "Exists: $dir\n";
    }
    chmod($dir, 0755);
}

// 2. Create storage link
echo "🔗 Creating storage link...\n";
$target = '../storage/app/public';
$link = 'public/storage';

// Remove if exists
if (file_exists($link) || is_link($link)) {
    unlink($link);
}

// Create symlink
if (symlink($target, $link)) {
    echo "✅ Storage link created successfully!\n";
} else {
    echo "❌ Failed to create storage link\n";
}

// 3. Clear cache files manually
echo "🧹 Clearing cache files...\n";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/services.php'
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Removed: $file\n";
    }
}

// Clear storage cache
$storageCacheDirs = [
    'storage/framework/cache/data',
    'storage/framework/views',
    'storage/framework/sessions'
];

foreach ($storageCacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "Cleared: $dir\n";
    }
}

// 4. Set proper permissions
echo "🔒 Setting permissions...\n";
$permissionDirs = [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'public/storage'
];

foreach ($permissionDirs as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        echo "Set permission 755: $dir\n";
    }
}

// 5. Test storage link
echo "🧪 Testing storage link...\n";
if (is_link('public/storage')) {
    echo "✅ Storage link is working\n";
    echo "Link target: " . readlink('public/storage') . "\n";
} else {
    echo "❌ Storage link not working\n";
}

// 6. Create test file
echo "📝 Creating test file...\n";
$testContent = "QR Code system test file - " . date('Y-m-d H:i:s');
file_put_contents('storage/app/public/test.txt', $testContent);

if (file_exists('public/storage/test.txt')) {
    echo "✅ Storage link test successful\n";
} else {
    echo "❌ Storage link test failed\n";
}

echo "\n";
echo "✅ Manual setup completed!\n";
echo "🌐 Next step: Run qr-generator.php\n";
echo "📋 Summary:\n";
echo "   - Directories created: " . count($directories) . "\n";
echo "   - Cache files cleared\n";
echo "   - Permissions set to 755\n";
echo "   - Storage link: " . (is_link('public/storage') ? 'OK' : 'FAILED') . "\n";
echo "\n";
?>
