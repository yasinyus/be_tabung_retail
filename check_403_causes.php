<?php

echo "=== CHECK 403 CAUSES ===\n\n";

// 1. Check web server configuration
echo "1️⃣  Web Server Check...\n";
echo "   Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "   Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "   Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'Unknown') . "\n";

// 2. Check .htaccess
echo "\n2️⃣  .htaccess Check...\n";
$htaccessPath = 'public/.htaccess';
if (file_exists($htaccessPath)) {
    echo "   ✅ .htaccess exists\n";
    $htaccessContent = file_get_contents($htaccessPath);
    if (strpos($htaccessContent, 'RewriteEngine On') !== false) {
        echo "   ✅ RewriteEngine is enabled\n";
    } else {
        echo "   ⚠️  RewriteEngine might be disabled\n";
    }
} else {
    echo "   ❌ .htaccess missing!\n";
    echo "   Creating basic .htaccess...\n";
    
    $basicHtaccess = '<IfModule mod_rewrite.c>
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
    
    file_put_contents($htaccessPath, $basicHtaccess);
    echo "   ✅ Basic .htaccess created\n";
}

// 3. Check file permissions
echo "\n3️⃣  File Permissions...\n";
$checkPaths = [
    'storage' => storage_path(),
    'bootstrap/cache' => 'bootstrap/cache',
    'public' => 'public',
    'app' => 'app'
];

foreach ($checkPaths as $name => $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path) ? 'WRITABLE' : 'NOT WRITABLE';
        echo "   {$name}: {$perms} ({$writable})\n";
    } else {
        echo "   {$name}: NOT FOUND\n";
    }
}

// 4. Check Laravel specific issues
echo "\n4️⃣  Laravel Issues...\n";

// Check APP_KEY
$appKey = env('APP_KEY');
echo "   APP_KEY: " . ($appKey ? 'SET' : 'NOT SET') . "\n";

// Check debug mode
$debug = env('APP_DEBUG', false);
echo "   APP_DEBUG: " . ($debug ? 'TRUE' : 'FALSE') . "\n";

// Check app environment
$env = env('APP_ENV', 'production');
echo "   APP_ENV: {$env}\n";

echo "\n5️⃣  Alternative URLs to Test...\n";
$baseUrl = env('APP_URL', 'http://localhost');
echo "   Try these URLs:\n";
echo "   1. {$baseUrl}/admin (main admin)\n";
echo "   2. {$baseUrl}/index.php/admin (with index.php)\n";
echo "   3. {$baseUrl}/admin/users (direct resource)\n";
echo "   4. {$baseUrl}/index.php/admin/users (with index.php)\n";

echo "\n6️⃣  PHP Information...\n";
echo "   PHP Version: " . phpversion() . "\n";
echo "   Memory Limit: " . ini_get('memory_limit') . "\n";
echo "   Upload Max: " . ini_get('upload_max_filesize') . "\n";

echo "\n🔧 MANUAL TESTS TO TRY:\n";
echo "1. Test with built-in server: php artisan serve\n";
echo "2. Check web server error logs\n";
echo "3. Try different browser/incognito mode\n";
echo "4. Check firewall/security software\n";
echo "5. Try accessing from different IP\n";

echo "\n⚠️  IF STILL 403 WITH SUPER PERMISSIVE SETTINGS:\n";
echo "The issue is likely NOT in Laravel code but in:\n";
echo "- Web server configuration (Apache/Nginx)\n";
echo "- Server-level permissions\n";
echo "- Firewall/security rules\n";
echo "- Reverse proxy settings\n";

?>
