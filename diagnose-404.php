<?php
// diagnose-404.php - Diagnose the 404 issue in local environment

echo "🔍 DIAGNOSING 404 ISSUE\n";
echo "======================\n\n";

// 1. Check environment
echo "1. 📋 Environment Check:\n";
$env = file_get_contents('.env');
if (strpos($env, 'APP_ENV=local') !== false) {
    echo "✅ APP_ENV=local\n";
} else {
    echo "❌ APP_ENV not set to local\n";
}

if (strpos($env, 'APP_DEBUG=true') !== false) {
    echo "✅ APP_DEBUG=true\n";
} else {
    echo "❌ APP_DEBUG not set to true\n";
}

// 2. Check if Laravel can bootstrap
echo "\n2. 🔧 Laravel Bootstrap Check:\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    echo "✅ Laravel bootstrap successful\n";
    
    // Get config
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ HTTP Kernel created\n";
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Check routes
echo "\n3. 🌐 Route Check:\n";
try {
    // Create a simple GET request to /
    $request = Illuminate\Http\Request::create('/', 'GET');
    $request->headers->set('Accept', 'text/html');
    
    echo "✅ Request created for '/'\n";
    
    // Try to handle the request
    $response = $kernel->handle($request);
    $statusCode = $response->getStatusCode();
    
    echo "Response Status: $statusCode\n";
    
    if ($statusCode == 200) {
        echo "✅ Root route working\n";
        
        // Check response content
        $content = $response->getContent();
        if (strpos($content, '404') !== false) {
            echo "⚠️  Warning: Response contains '404' in content\n";
            
            // Find where the 404 is coming from
            if (strpos($content, 'Not Found') !== false) {
                echo "🔍 Contains 'Not Found' text\n";
            }
            if (strpos($content, 'Sorry, the page you are looking for') !== false) {
                echo "🔍 Contains generic 404 message\n";
            }
        } else {
            echo "✅ No 404 content detected\n";
        }
        
        // Check if it's the welcome page
        if (strpos($content, 'Laravel') !== false) {
            echo "✅ Laravel welcome page detected\n";
        }
        
    } elseif ($statusCode == 404) {
        echo "❌ Root route returns 404\n";
    } else {
        echo "⚠️  Unexpected status code: $statusCode\n";
    }
    
} catch (Exception $e) {
    echo "❌ Route test failed: " . $e->getMessage() . "\n";
}

// 4. Check view
echo "\n4. 👁️  View Check:\n";
if (file_exists('resources/views/welcome.blade.php')) {
    echo "✅ welcome.blade.php exists\n";
    
    // Check if view can be compiled
    try {
        $viewContent = file_get_contents('resources/views/welcome.blade.php');
        if (strpos($viewContent, '404') !== false) {
            echo "⚠️  WARNING: welcome.blade.php contains '404' text\n";
            
            // Find line numbers with 404
            $lines = explode("\n", $viewContent);
            foreach ($lines as $lineNum => $line) {
                if (strpos($line, '404') !== false) {
                    echo "   Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
                }
            }
        } else {
            echo "✅ No 404 content in welcome.blade.php\n";
        }
    } catch (Exception $e) {
        echo "❌ View check failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ welcome.blade.php not found\n";
}

// 5. Check cache
echo "\n5. 🗂️  Cache Check:\n";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/services.php',
    'storage/framework/views'
];

foreach ($cacheFiles as $cache) {
    if (file_exists($cache)) {
        echo "⚠️  Cache exists: $cache\n";
        if (is_file($cache)) {
            unlink($cache);
            echo "   🗑️  Removed\n";
        }
    } else {
        echo "✅ Cache clear: $cache\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 DIAGNOSIS COMPLETE\n";
echo str_repeat("=", 50) . "\n";

echo "💡 SUGGESTIONS:\n";
echo "1. If welcome.blade.php contains 404 text, that's the issue\n";
echo "2. If routes are working but showing 404, check view compilation\n";
echo "3. Try: php artisan view:clear\n";
echo "4. Try: php artisan route:clear\n";
echo "5. Try: php artisan config:clear\n\n";

echo "🚀 Next: Start server with 'php artisan serve' and test again\n";
?>
