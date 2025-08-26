<?php

echo "=== SIMPLE DEBUG 403 FORBIDDEN ===\n\n";

// 1. Basic file checks
echo "1️⃣  Basic File Checks...\n";

$criticalFiles = [
    'composer.json' => 'Composer config',
    'vendor/autoload.php' => 'Composer autoload',
    'vendor/filament' => 'Filament package',
    'app/Providers/Filament/AdminPanelProvider.php' => 'Admin Provider',
    '.env' => 'Environment config',
    'public/.htaccess' => 'Apache rewrite rules',
    'storage/logs' => 'Log directory',
    'bootstrap/cache' => 'Cache directory',
];

foreach ($criticalFiles as $file => $desc) {
    if (file_exists($file)) {
        echo "   ✅ {$desc}: EXISTS\n";
    } else {
        echo "   ❌ {$desc}: MISSING\n";
    }
}

// 2. Check permissions
echo "\n2️⃣  Permission Checks...\n";
$permissionPaths = ['storage', 'bootstrap/cache', 'public'];

foreach ($permissionPaths as $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path) ? 'WRITABLE' : 'NOT WRITABLE';
        echo "   {$path}: {$perms} ({$writable})\n";
    }
}

// 3. Check PHP requirements
echo "\n3️⃣  PHP Environment...\n";
echo "   PHP Version: " . phpversion() . "\n";
echo "   Memory Limit: " . ini_get('memory_limit') . "\n";
echo "   Max Execution: " . ini_get('max_execution_time') . "\n";

$requiredExtensions = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
foreach ($requiredExtensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "   {$status} {$ext}\n";
}

// 4. Check .env file
echo "\n4️⃣  Environment Variables...\n";
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    $envLines = explode("\n", $envContent);
    
    $importantVars = ['APP_ENV', 'APP_DEBUG', 'APP_URL', 'APP_KEY'];
    foreach ($importantVars as $var) {
        $found = false;
        foreach ($envLines as $line) {
            if (strpos($line, $var . '=') === 0) {
                echo "   ✅ {$line}\n";
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "   ❌ {$var}: NOT SET\n";
        }
    }
} else {
    echo "   ❌ .env file missing!\n";
}

// 5. Check composer.json
echo "\n5️⃣  Dependency Check...\n";
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    
    $importantPackages = [
        'laravel/framework',
        'filament/filament',
        'spatie/laravel-permission'
    ];
    
    foreach ($importantPackages as $package) {
        if (isset($composer['require'][$package])) {
            echo "   ✅ {$package}: " . $composer['require'][$package] . "\n";
        } else {
            echo "   ❌ {$package}: NOT FOUND\n";
        }
    }
}

// 6. Server information
echo "\n6️⃣  Server Information...\n";
echo "   Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "   Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "   Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "\n";
echo "   HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "\n";

// 7. Common fixes
echo "\n🔧 COMMON FIXES TO TRY:\n";
echo "1. composer install --optimize-autoloader\n";
echo "2. php artisan config:clear\n";
echo "3. php artisan route:clear\n";
echo "4. php artisan cache:clear\n";
echo "5. chmod -R 755 storage bootstrap/cache\n";
echo "6. chown -R www-data:www-data . (on Linux)\n";
echo "7. Check web server error logs\n";
echo "8. Try direct PHP: php -S localhost:8000 -t public\n";

echo "\n🚀 NEXT STEPS:\n";
echo "1. Run: php nuclear_option_fix.php\n";
echo "2. Test: http://your-domain/admin\n";
echo "3. Check server error logs if still 403\n";

?>
