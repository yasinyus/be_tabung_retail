<?php

// Production Server Fix Script
// Jalankan: php fix_server_production.php

echo "🔧 PT GAS API - Production Server Fix\n";
echo "=====================================\n\n";

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
    '.env'
];

foreach ($filesToBackup as $file) {
    if (file_exists($file)) {
        copy($file, $file . '.backup.' . date('Y-m-d-H-i-s'));
        echo "✅ Backup created: {$file}.backup." . date('Y-m-d-H-i-s') . "\n";
    }
}

// Step 2: Fix bootstrap/app.php
echo "\n🔧 Fixing bootstrap/app.php...\n";
$bootstrapContent = file_get_contents('bootstrap/app.php');

// Remove problematic exception handler completely
$fixedContent = preg_replace(
    '/->withExceptions\(function \(Exceptions \$exceptions\): void \{[\s\S]*?\}\)/',
    '',
    $bootstrapContent
);

// Add proper exception handler
$exceptionHandler = '
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle API exceptions
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is(\'api/*\') || $request->wantsJson()) {
                return response()->json([
                    \'status\' => \'error\',
                    \'message\' => \'Unauthenticated.\'
                ], 401);
            }
            
            return redirect()->guest(\'/admin/login\');
        });
    })';

// Insert exception handler before ->create()
$fixedContent = str_replace('->create();', $exceptionHandler . '->create();', $fixedContent);

file_put_contents('bootstrap/app.php', $fixedContent);
echo "✅ bootstrap/app.php fixed\n";

// Step 3: Clear all caches
echo "\n🧹 Clearing caches...\n";
$commands = [
    'php artisan cache:clear',
    'php artisan config:clear', 
    'php artisan route:clear',
    'php artisan view:clear',
    'php artisan optimize:clear'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    system($command . ' 2>&1', $returnCode);
    if ($returnCode === 0) {
        echo "✅ $command completed\n";
    } else {
        echo "⚠️ $command had issues (this might be normal)\n";
    }
}

// Step 4: Rebuild caches
echo "\n🔨 Rebuilding caches...\n";
$rebuildCommands = [
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache'
];

foreach ($rebuildCommands as $command) {
    echo "Running: $command\n";
    system($command . ' 2>&1', $returnCode);
    if ($returnCode === 0) {
        echo "✅ $command completed\n";
    } else {
        echo "⚠️ $command had issues\n";
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
    system($command . ' 2>&1', $returnCode);
    if ($returnCode === 0) {
        echo "✅ $command completed\n";
    } else {
        echo "⚠️ $command had issues (might need sudo)\n";
    }
}

// Step 6: Test API
echo "\n🧪 Testing API...\n";
$testUrl = 'http://localhost/api/v1/test';
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "✅ API test endpoint working (HTTP $httpCode)\n";
    } else {
        echo "⚠️ API test endpoint returned HTTP $httpCode\n";
    }
} else {
    echo "⚠️ cURL not available, cannot test API\n";
}

echo "\n🎉 Fix completed!\n";
echo "=====================================\n";
echo "Next steps:\n";
echo "1. Test your API endpoints\n";
echo "2. Check logs if issues persist\n";
echo "3. Restart web server if needed\n";
echo "\nTest commands:\n";
echo "curl -I http://your-domain.com/api/v1/test\n";
echo "curl -X POST http://your-domain.com/api/v1/auth/login -H 'Content-Type: application/json' -d '{\"email\":\"admin@example.com\",\"password\":\"password\"}'\n";
