<?php

echo "=== SIMPLE 403 CHECK ===\n\n";

// 1. Basic file checks tanpa Laravel functions
echo "1️⃣  Basic File Checks...\n";

$paths = [
    'storage' => is_dir('storage') && is_writable('storage'),
    'bootstrap/cache' => is_dir('bootstrap/cache') && is_writable('bootstrap/cache'),
    'public' => is_dir('public') && is_readable('public'),
    'app' => is_dir('app') && is_readable('app'),
    '.env' => file_exists('.env') && is_readable('.env')
];

foreach ($paths as $name => $status) {
    $icon = $status ? '✅' : '❌';
    echo "   {$icon} {$name}\n";
}

// 2. Check .htaccess content
echo "\n2️⃣  .htaccess Analysis...\n";
if (file_exists('public/.htaccess')) {
    $htaccess = file_get_contents('public/.htaccess');
    
    $checks = [
        'RewriteEngine On' => strpos($htaccess, 'RewriteEngine On') !== false,
        'Authorization Header' => strpos($htaccess, 'HTTP_AUTHORIZATION') !== false,
        'Front Controller' => strpos($htaccess, 'index.php') !== false,
    ];
    
    foreach ($checks as $name => $exists) {
        $icon = $exists ? '✅' : '❌';
        echo "   {$icon} {$name}\n";
    }
} else {
    echo "   ❌ .htaccess not found in public/\n";
}

// 3. Check environment file
echo "\n3️⃣  Environment Check...\n";
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    
    $envChecks = [
        'APP_KEY' => strpos($env, 'APP_KEY=') !== false && strpos($env, 'APP_KEY=base64:') !== false,
        'APP_URL' => strpos($env, 'APP_URL=') !== false,
        'APP_DEBUG' => strpos($env, 'APP_DEBUG=') !== false,
        'DB_CONNECTION' => strpos($env, 'DB_CONNECTION=') !== false
    ];
    
    foreach ($envChecks as $name => $exists) {
        $icon = $exists ? '✅' : '❌';
        echo "   {$icon} {$name}\n";
    }
} else {
    echo "   ❌ .env file not found!\n";
}

// 4. Check composer and autoload
echo "\n4️⃣  Composer Check...\n";
$composerChecks = [
    'composer.json' => file_exists('composer.json'),
    'vendor/autoload.php' => file_exists('vendor/autoload.php'),
    'vendor/filament' => is_dir('vendor/filament')
];

foreach ($composerChecks as $name => $exists) {
    $icon = $exists ? '✅' : '❌';
    echo "   {$icon} {$name}\n";
}

// 5. Check bootstrap files
echo "\n5️⃣  Bootstrap Check...\n";
$bootstrapChecks = [
    'bootstrap/app.php' => file_exists('bootstrap/app.php'),
    'bootstrap/providers.php' => file_exists('bootstrap/providers.php'),
    'public/index.php' => file_exists('public/index.php')
];

foreach ($bootstrapChecks as $name => $exists) {
    $icon = $exists ? '✅' : '❌';
    echo "   {$icon} {$name}\n";
}

echo "\n🔧 IMMEDIATE ACTIONS TO TRY:\n";
echo "1. Run: composer install --optimize-autoloader\n";
echo "2. Run: php artisan config:clear\n";
echo "3. Run: php artisan cache:clear\n";
echo "4. Test URL: http://8.215.70.68/index.php/admin\n";
echo "5. Check if mod_rewrite is enabled on server\n";

echo "\n🌐 ALTERNATIVE TESTS:\n";
echo "A. Test built-in server:\n";
echo "   php artisan serve --host=0.0.0.0 --port=8001\n";
echo "   Then test: http://8.215.70.68:8001/admin\n";

echo "\nB. Test direct index.php:\n";
echo "   http://8.215.70.68/index.php/admin\n";
echo "   http://8.215.70.68/index.php/admin/users\n";

echo "\nC. Check web server status:\n";
echo "   sudo systemctl status apache2\n";
echo "   sudo systemctl status nginx\n";

echo "\n⚠️  IF BASIC LARAVEL IS WORKING BUT ADMIN IS 403:\n";
echo "The issue might be in Filament routing or middleware.\n";
echo "Try accessing the basic Laravel welcome page first.\n";

echo "\n🔍 DEBUG PRIORITY:\n";
echo "1. Fix any ❌ issues above\n";
echo "2. Test with index.php in URL\n";
echo "3. Try built-in PHP server\n";
echo "4. Check web server logs\n";

?>
