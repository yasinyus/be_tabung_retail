<?php

// Ultimate Server Fix Script untuk Route [login] not defined
// Jalankan: php fix_server_ultimate.php

echo "🚀 PT GAS API - Ultimate Server Fix\n";
echo "===================================\n\n";

// Check if we're in the right directory
if (!file_exists('bootstrap/app.php')) {
    echo "❌ Error: bootstrap/app.php not found. Please run this script from the project root.\n";
    exit(1);
}

echo "✅ Project directory confirmed\n";

// Step 1: Backup original files
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

// Step 2: Fix bootstrap/app.php - Remove ALL exception handlers
echo "\n🔧 Fixing bootstrap/app.php...\n";
$bootstrapContent = file_get_contents('bootstrap/app.php');

// Remove ALL withExceptions blocks completely
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
echo "✅ bootstrap/app.php fixed - ALL exception handlers removed\n";

// Step 3: Add explicit login route to web.php
echo "\n🔧 Adding explicit login route to web.php...\n";
$webContent = file_get_contents('routes/web.php');

// Check if login route already exists
if (strpos($webContent, 'Route::get(\'login\'') === false && strpos($webContent, 'Route::post(\'login\'') === false) {
    $loginRoutes = "\n// Explicit login routes to prevent Route [login] not defined error\n";
    $loginRoutes .= "Route::get('/login', function () {\n";
    $loginRoutes .= "    return redirect('/admin/login');\n";
    $loginRoutes .= "})->name('login');\n\n";
    $loginRoutes .= "Route::post('/login', function () {\n";
    $loginRoutes .= "    return redirect('/admin/login');\n";
    $loginRoutes .= "})->name('login.post');\n\n";
    
    // Insert before the last closing PHP tag or at the end
    if (strpos($webContent, '?>') !== false) {
        $webContent = str_replace('?>', $loginRoutes . '?>', $webContent);
    } else {
        $webContent .= $loginRoutes;
    }
    
    file_put_contents('routes/web.php', $webContent);
    echo "✅ Login routes added to web.php\n";
} else {
    echo "✅ Login routes already exist in web.php\n";
}

// Step 4: Clear ALL caches aggressively
echo "\n🧹 Clearing ALL caches aggressively...\n";
$clearCommands = [
    'php artisan cache:clear',
    'php artisan config:clear', 
    'php artisan route:clear',
    'php artisan view:clear',
    'php artisan optimize:clear',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache'
];

foreach ($clearCommands as $command) {
    echo "Running: $command\n";
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ $command completed\n";
    } else {
        echo "⚠️ $command had issues (output: " . implode(' ', $output) . ")\n";
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

// Step 7: Test API endpoint directly
echo "\n🧪 Testing API endpoint...\n";
$testUrl = 'http://localhost/api/v1/test';
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "⚠️ cURL error: $error\n";
    } else if ($httpCode === 200) {
        echo "✅ API test endpoint working (HTTP $httpCode)\n";
    } else {
        echo "⚠️ API test endpoint returned HTTP $httpCode\n";
        echo "Response: $response\n";
    }
} else {
    echo "⚠️ cURL not available, cannot test API\n";
}

// Step 8: Create emergency route test
echo "\n🔧 Creating emergency route test...\n";
$emergencyTest = '<?php
// Emergency route test
// Access: http://your-domain.com/emergency-test.php

echo "<h1>Emergency Route Test</h1>";

try {
    // Test basic Laravel
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    echo "<p>✅ Laravel app loaded</p>";
    
    // Test route generation
    $router = $app->make("router");
    echo "<p>✅ Router loaded</p>";
    
    // Test specific routes
    $routes = [
        "/api/v1/test",
        "/api/v1/auth/login", 
        "/api/v1/mobile/terima-tabung"
    ];
    
    foreach ($routes as $route) {
        try {
            $request = Illuminate\Http\Request::create($route, "GET");
            $response = $app->handle($request);
            echo "<p>✅ Route $route: HTTP " . $response->getStatusCode() . "</p>";
        } catch (Exception $e) {
            echo "<p>❌ Route $route: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>';

file_put_contents('emergency-test.php', $emergencyTest);
echo "✅ Emergency test created: emergency-test.php\n";

echo "\n🎉 Ultimate fix completed!\n";
echo "===================================\n";
echo "Next steps:\n";
echo "1. Restart web server: sudo systemctl restart nginx/apache2\n";
echo "2. Test emergency route: http://your-domain.com/emergency-test.php\n";
echo "3. Test API: curl -I http://your-domain.com/api/v1/test\n";
echo "4. Test terima-tabung: curl -X POST http://your-domain.com/api/v1/mobile/terima-tabung\n";
echo "\nIf still having issues:\n";
echo "1. Check logs: tail -f storage/logs/laravel.log\n";
echo "2. Check web server logs\n";
echo "3. Verify .env configuration\n";
echo "\nEmergency commands:\n";
echo "php artisan route:clear && php artisan config:clear && php artisan cache:clear\n";
echo "sudo systemctl restart nginx\n";
echo "sudo systemctl restart php8.2-fpm\n";
