<?php

echo "🔧 Emergency Server Route Login Fix\n\n";

// Check if we're on server or local
$isServer = !str_contains(__DIR__, 'laragon');

if (!$isServer) {
    echo "❌ This script should only run on SERVER, not local!\n";
    exit(1);
}

echo "✅ Running on SERVER environment\n\n";

// Check current bootstrap/app.php content
$bootstrapFile = __DIR__ . '/bootstrap/app.php';

if (!file_exists($bootstrapFile)) {
    echo "❌ bootstrap/app.php not found!\n";
    exit(1);
}

$content = file_get_contents($bootstrapFile);

// Check if our fix is already there
if (strpos($content, 'AuthenticationException') !== false) {
    echo "✅ Route login fix already applied in bootstrap/app.php\n";
} else {
    echo "❌ Route login fix NOT found in bootstrap/app.php\n";
    echo "📝 Applying fix...\n\n";
    
    // Apply the fix
    $originalContent = '    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();';
    
    $fixedContent = '    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle unauthenticated redirects for Filament admin
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            // For Filament admin routes, redirect to the correct login route
            if ($request->routeIs(\'filament.*\')) {
                return redirect()->guest(route(\'filament.admin.auth.login\'));
            }
            
            // For API routes, return JSON response
            if ($request->is(\'api/*\') || $request->wantsJson()) {
                return response()->json([
                    \'message\' => \'Unauthenticated.\'
                ], 401);
            }
            
            // Default redirect for web routes (using named route)
            return redirect()->guest(route(\'filament.admin.auth.login\'));
        });
    })->create();';
    
    $newContent = str_replace($originalContent, $fixedContent, $content);
    
    if ($newContent !== $content) {
        file_put_contents($bootstrapFile, $newContent);
        echo "✅ Fix applied to bootstrap/app.php\n";
    } else {
        echo "❌ Could not apply fix - content mismatch\n";
        echo "Current withExceptions section:\n";
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            if (strpos($line, 'withExceptions') !== false) {
                for ($j = max(0, $i-2); $j <= min(count($lines)-1, $i+5); $j++) {
                    echo ($j+1) . ": " . $lines[$j] . "\n";
                }
                break;
            }
        }
        exit(1);
    }
}

echo "\n🔄 Clearing server cache...\n";

// Clear all cache
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear', 
    'php artisan route:clear',
    'php artisan view:clear',
    'composer dump-autoload --optimize'
];

foreach ($commands as $cmd) {
    echo "Running: $cmd\n";
    $output = shell_exec($cmd . ' 2>&1');
    echo $output . "\n";
}

echo "\n✅ Server route login fix completed!\n";
echo "🎯 Try accessing admin panel now\n";
