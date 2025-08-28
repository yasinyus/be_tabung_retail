<?php

// Fix Script untuk Server 8.215.70.68
// Jalankan: php fix_server_8_215_70_68.php

echo "🔧 PT GAS API - Server 8.215.70.68 Fix\n";
echo "=======================================\n\n";

// Check if we're in the right directory
if (!file_exists('bootstrap/app.php')) {
    echo "❌ Error: bootstrap/app.php not found. Please run this script from the project root.\n";
    exit(1);
}

echo "✅ Project directory confirmed\n";

// Step 1: Backup files
echo "\n📦 Creating backups...\n";
$filesToBackup = [
    'bootstrap/app.php',
    '.env',
    'routes/web.php',
    'routes/api.php'
];

foreach ($filesToBackup as $file) {
    if (file_exists($file)) {
        $backupName = $file . '.backup.' . date('Y-m-d-H-i-s');
        copy($file, $backupName);
        echo "✅ Backup created: $backupName\n";
    }
}

// Step 2: Fix bootstrap/app.php
echo "\n🔧 Fixing bootstrap/app.php...\n";
$bootstrapContent = file_get_contents('bootstrap/app.php');

// Remove any problematic withExceptions blocks
$fixedContent = preg_replace(
    '/->withExceptions\(function \(Exceptions \$exceptions\): void \{[\s\S]*?\}\)/',
    '',
    $bootstrapContent
);

// Remove any remaining withExceptions calls
$fixedContent = preg_replace(
    '/->withExceptions\([^)]*\)/',
    '',
    $fixedContent
);

file_put_contents('bootstrap/app.php', $fixedContent);
echo "✅ bootstrap/app.php fixed - exception handlers removed\n";

// Step 3: Add login route to web.php
echo "\n🔧 Adding login route to web.php...\n";
$webContent = file_get_contents('routes/web.php');

if (strpos($webContent, 'Route::get(\'/login\'') === false) {
    $loginRoute = "\n// Login route to prevent Route [login] not defined error\n";
    $loginRoute .= "Route::get('/login', function () {\n";
    $loginRoute .= "    return redirect('/admin/login');\n";
    $loginRoute .= "})->name('login');\n\n";
    
    $webContent .= $loginRoute;
    file_put_contents('routes/web.php', $webContent);
    echo "✅ Login route added to web.php\n";
} else {
    echo "✅ Login route already exists in web.php\n";
}

// Step 4: Clear all caches
echo "\n🧹 Clearing all caches...\n";
$clearCommands = [
    'php artisan cache:clear',
    'php artisan config:clear',
    'php artisan route:clear',
    'php artisan view:clear',
    'php artisan optimize:clear'
];

foreach ($clearCommands as $command) {
    echo "Running: $command\n";
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ $command completed\n";
    } else {
        echo "⚠️ $command had issues (this might be normal)\n";
    }
}

// Step 5: Fix permissions
echo "\n🔐 Fixing permissions...\n";
$permissionCommands = [
    'chmod -R 755 .',
    'chmod -R 775 storage',
    'chmod -R 775 bootstrap/cache'
];

foreach ($permissionCommands as $command) {
    echo "Running: $command\n";
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ $command completed\n";
    } else {
        echo "⚠️ $command had issues (might need sudo)\n";
    }
}

// Step 6: Test route list
echo "\n🧪 Testing route list...\n";
$output = [];
$returnCode = 0;
exec('php artisan route:list --path=api 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ Route list working\n";
    foreach ($output as $line) {
        if (strpos($line, 'terima-tabung') !== false) {
            echo "   Found: $line\n";
        }
    }
} else {
    echo "❌ Route list failed: " . implode(' ', $output) . "\n";
}

// Step 7: Create server-specific test
echo "\n🔧 Creating server test...\n";
$serverTest = '<?php
// Server 8.215.70.68 Test
// Access: http://8.215.70.68/server-test.php

echo "<h1>Server 8.215.70.68 - PT GAS API Test</h1>";

try {
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    echo "<p>✅ Laravel app loaded</p>";
    
    // Test route generation
    try {
        $url = route("login");
        echo "<p>✅ Route login generated: $url</p>";
    } catch (Exception $e) {
        echo "<p>❌ Route login failed: " . $e->getMessage() . "</p>";
    }
    
    // Test API routes
    $apiRoutes = [
        "/api/v1/test",
        "/api/v1/auth/login",
        "/api/v1/mobile/terima-tabung"
    ];
    
    foreach ($apiRoutes as $route) {
        try {
            $request = Illuminate\Http\Request::create($route, "GET");
            $response = $app->handle($request);
            echo "<p>✅ Route $route: HTTP " . $response->getStatusCode() . "</p>";
        } catch (Exception $e) {
            echo "<p>❌ Route $route: " . $e->getMessage() . "</p>";
        }
    }
    
    // Test terima-tabung specifically
    try {
        $request = Illuminate\Http\Request::create("/api/v1/mobile/terima-tabung", "POST");
        $response = $app->handle($request);
        echo "<p>✅ Terima-tabung POST: HTTP " . $response->getStatusCode() . "</p>";
    } catch (Exception $e) {
        echo "<p>❌ Terima-tabung POST: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>';

file_put_contents('server-test.php', $serverTest);
echo "✅ Server test created: server-test.php\n";

echo "\n🎉 Server fix completed!\n";
echo "=======================================\n";
echo "Next steps:\n";
echo "1. Test server: http://8.215.70.68/server-test.php\n";
echo "2. Test API: curl -X POST http://8.215.70.68/api/v1/mobile/terima-tabung\n";
echo "3. Restart web server if needed\n";
echo "\nTest commands:\n";
echo "curl -I http://8.215.70.68/api/v1/test\n";
echo "curl -X POST http://8.215.70.68/api/v1/mobile/terima-tabung -H 'Content-Type: application/json' -d '{\"test\":\"data\"}'\n";
echo "\nIf still having issues:\n";
echo "1. Check logs: tail -f storage/logs/laravel.log\n";
echo "2. Restart web server: sudo systemctl restart nginx\n";
echo "3. Check web server logs: tail -f /var/log/nginx/error.log\n";
