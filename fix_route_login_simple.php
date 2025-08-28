<?php

// Simple Fix untuk Route [login] not defined
// Jalankan: php fix_route_login_simple.php

echo "🔧 PT GAS API - Simple Route Login Fix\n";
echo "======================================\n\n";

// Check if we're in the right directory
if (!file_exists('bootstrap/app.php')) {
    echo "❌ Error: bootstrap/app.php not found. Please run this script from the project root.\n";
    exit(1);
}

echo "✅ Project directory confirmed\n";

// Step 1: Backup bootstrap/app.php
echo "\n📦 Creating backup...\n";
$backupName = 'bootstrap/app.php.backup.' . date('Y-m-d-H-i-s');
if (copy('bootstrap/app.php', $backupName)) {
    echo "✅ Backup created: $backupName\n";
} else {
    echo "❌ Failed to create backup\n";
    exit(1);
}

// Step 2: Read current bootstrap/app.php
echo "\n🔧 Reading bootstrap/app.php...\n";
$bootstrapContent = file_get_contents('bootstrap/app.php');

// Step 3: Check if withExceptions exists and modify it
if (strpos($bootstrapContent, 'withExceptions') !== false) {
    echo "✅ Found withExceptions block, modifying...\n";
    
    // Replace the problematic redirect with a simple one
    $fixedContent = preg_replace(
        '/return redirect\(\)->guest\(route\(\'login\'\)\);/',
        'return redirect()->guest(\'/admin/login\');',
        $bootstrapContent
    );
    
    // Also replace any other route('login') references
    $fixedContent = preg_replace(
        '/route\(\'login\'\)/',
        '\'/admin/login\'',
        $fixedContent
    );
    
    if ($fixedContent !== $bootstrapContent) {
        file_put_contents('bootstrap/app.php', $fixedContent);
        echo "✅ bootstrap/app.php fixed\n";
    } else {
        echo "⚠️ No changes needed in bootstrap/app.php\n";
    }
} else {
    echo "⚠️ No withExceptions block found\n";
}

// Step 4: Add login route to web.php if not exists
echo "\n🔧 Checking web.php for login routes...\n";
$webContent = file_get_contents('routes/web.php');

if (strpos($webContent, 'Route::get(\'/login\'') === false) {
    echo "✅ Adding login route to web.php...\n";
    
    $loginRoute = "\n// Simple login route to prevent Route [login] not defined error\n";
    $loginRoute .= "Route::get('/login', function () {\n";
    $loginRoute .= "    return redirect('/admin/login');\n";
    $loginRoute .= "})->name('login');\n\n";
    
    // Add at the end of the file
    $webContent .= $loginRoute;
    file_put_contents('routes/web.php', $webContent);
    echo "✅ Login route added to web.php\n";
} else {
    echo "✅ Login route already exists in web.php\n";
}

// Step 5: Clear caches
echo "\n🧹 Clearing caches...\n";
$clearCommands = [
    'php artisan cache:clear',
    'php artisan config:clear',
    'php artisan route:clear',
    'php artisan view:clear'
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

// Step 7: Create simple test
echo "\n🔧 Creating simple test...\n";
$simpleTest = '<?php
// Simple test untuk Route [login] not defined
// Access: http://your-domain.com/simple-test.php

echo "<h1>Simple Route Test</h1>";

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
    
    // Test API route
    try {
        $request = Illuminate\Http\Request::create("/api/v1/mobile/terima-tabung", "POST");
        $response = $app->handle($request);
        echo "<p>✅ API route working: HTTP " . $response->getStatusCode() . "</p>";
    } catch (Exception $e) {
        echo "<p>❌ API route failed: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>';

file_put_contents('simple-test.php', $simpleTest);
echo "✅ Simple test created: simple-test.php\n";

echo "\n🎉 Simple fix completed!\n";
echo "======================================\n";
echo "Next steps:\n";
echo "1. Test simple route: http://your-domain.com/simple-test.php\n";
echo "2. Test API: curl -X POST http://your-domain.com/api/v1/mobile/terima-tabung\n";
echo "3. If needed, restart web server\n";
echo "\nTest commands:\n";
echo "curl -X POST http://your-domain.com/api/v1/mobile/terima-tabung -H 'Content-Type: application/json' -d '{\"test\":\"data\"}'\n";
echo "\nIf still having issues:\n";
echo "1. Check logs: tail -f storage/logs/laravel.log\n";
echo "2. Restart web server: sudo systemctl restart nginx\n";
