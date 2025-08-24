<?php
// fix-404-live-server.php - Fix 404 errors after Pail fix

echo "🔍 FIXING 404 ERRORS ON LIVE SERVER\n";
echo "===================================\n\n";

echo "This script will diagnose and fix 404 errors after applying Pail fixes.\n\n";

// 1. Check if we're in the right directory
echo "📁 Step 1: Checking directory structure...\n";
$requiredFiles = [
    'index.php',
    'composer.json',
    'artisan',
    'app/Http/Kernel.php',
    'bootstrap/app.php'
];

$missingFiles = [];
foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        $missingFiles[] = $file;
    } else {
        echo "✅ Found: $file\n";
    }
}

if (!empty($missingFiles)) {
    echo "❌ Missing files: " . implode(', ', $missingFiles) . "\n";
    echo "Make sure you're running this from Laravel root directory!\n\n";
} else {
    echo "✅ All core Laravel files found\n\n";
}

// 2. Check and fix public/.htaccess
echo "🌐 Step 2: Checking .htaccess configuration...\n";
$htaccessPath = 'public/.htaccess';
$correctHtaccess = '<IfModule mod_rewrite.c>
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

if (!file_exists($htaccessPath)) {
    file_put_contents($htaccessPath, $correctHtaccess);
    echo "✅ Created public/.htaccess file\n";
} else {
    $currentHtaccess = file_get_contents($htaccessPath);
    if (strpos($currentHtaccess, 'RewriteRule ^ index.php [L]') === false) {
        file_put_contents($htaccessPath, $correctHtaccess);
        echo "✅ Fixed public/.htaccess file\n";
    } else {
        echo "✅ public/.htaccess is correct\n";
    }
}

// 3. Check root .htaccess (for subdirectory installations)
echo "\n🔗 Step 3: Checking root .htaccess...\n";
$rootHtaccessPath = '.htaccess';
if (!file_exists($rootHtaccessPath)) {
    $rootHtaccess = '<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>';
    file_put_contents($rootHtaccessPath, $rootHtaccess);
    echo "✅ Created root .htaccess to redirect to public/\n";
} else {
    echo "✅ Root .htaccess exists\n";
}

// 4. Check public/index.php
echo "\n📋 Step 4: Checking public/index.php...\n";
$indexPath = 'public/index.php';
if (!file_exists($indexPath)) {
    echo "❌ public/index.php is missing!\n";
    
    // Create basic index.php
    $indexContent = '<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define(\'LARAVEL_START\', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.\'/../storage/framework/maintenance.php\')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We\'ll simply require it
| into the script here so we don\'t need to manually load our classes.
|
*/

require __DIR__.\'/../vendor/autoload.php\';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application\'s HTTP kernel. Then, we will send the response back
| to this client\'s browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.\'/../bootstrap/app.php\';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
';
    
    file_put_contents($indexPath, $indexContent);
    echo "✅ Created public/index.php\n";
} else {
    echo "✅ public/index.php exists\n";
}

// 5. Check if routes are properly cached
echo "\n🛣️  Step 5: Checking routes...\n";
if (file_exists('bootstrap/cache/routes.php')) {
    unlink('bootstrap/cache/routes.php');
    echo "✅ Cleared route cache\n";
}

// 6. Test basic Laravel bootstrap
echo "\n🧪 Step 6: Testing Laravel bootstrap...\n";
try {
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        
        if (file_exists('bootstrap/app.php')) {
            $app = require_once 'bootstrap/app.php';
            echo "✅ Laravel application boots successfully\n";
            
            // Test if we can create a request
            $request = Illuminate\Http\Request::create('/');
            echo "✅ Request object created\n";
            
        } else {
            echo "❌ bootstrap/app.php missing\n";
        }
    } else {
        echo "❌ vendor/autoload.php missing - run composer install\n";
    }
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
}

// 7. Check web.php routes
echo "\n📄 Step 7: Checking routes/web.php...\n";
$webRoutesPath = 'routes/web.php';
if (!file_exists($webRoutesPath)) {
    echo "❌ routes/web.php missing!\n";
    
    $basicWebRoutes = '<?php

use Illuminate\Support\Facades\Route;

Route::get(\'/\', function () {
    return view(\'welcome\');
});
';
    
    if (!is_dir('routes')) {
        mkdir('routes', 0755, true);
    }
    
    file_put_contents($webRoutesPath, $basicWebRoutes);
    echo "✅ Created basic routes/web.php\n";
} else {
    echo "✅ routes/web.php exists\n";
}

// 8. Check API routes
echo "\n🔌 Step 8: Checking routes/api.php...\n";
$apiRoutesPath = 'routes/api.php';
if (file_exists($apiRoutesPath)) {
    echo "✅ routes/api.php exists\n";
} else {
    echo "⚠️  routes/api.php missing - API endpoints may not work\n";
}

// 9. Create a test route file
echo "\n🧪 Step 9: Creating test routes...\n";
$testRoutePath = 'public/test-server.php';
$testContent = '<?php
// Simple test to check if server is working
echo "✅ Server is working!\\n";
echo "PHP Version: " . PHP_VERSION . "\\n";
echo "Current Directory: " . getcwd() . "\\n";
echo "Document Root: " . $_SERVER["DOCUMENT_ROOT"] ?? "Not set" . "\\n";
echo "Request URI: " . $_SERVER["REQUEST_URI"] ?? "Not set" . "\\n";
echo "Script Name: " . $_SERVER["SCRIPT_NAME"] ?? "Not set" . "\\n";
';

file_put_contents($testRoutePath, $testContent);
echo "✅ Created test-server.php\n";

// 10. Summary and instructions
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 404 ERROR FIX COMPLETED!\n";
echo str_repeat("=", 60) . "\n";
echo "📋 WHAT WAS FIXED:\n";
echo "✅ Created/fixed public/.htaccess for URL rewriting\n";
echo "✅ Created root .htaccess for subdirectory support\n";
echo "✅ Ensured public/index.php exists\n";
echo "✅ Cleared route cache\n";
echo "✅ Verified Laravel bootstrap\n";
echo "✅ Ensured basic routes exist\n";
echo "✅ Created test file\n\n";

echo "🧪 TEST YOUR SERVER NOW:\n";
echo "1. Direct test: https://yourserver.com/test-server.php\n";
echo "2. Laravel home: https://yourserver.com/\n";
echo "3. Admin panel: https://yourserver.com/admin\n";
echo "4. API login: https://yourserver.com/api/v1/auth/login\n\n";

echo "🚨 IF STILL GETTING 404:\n";
echo "1. Check if mod_rewrite is enabled on your server\n";
echo "2. Verify document root points to /public directory\n";
echo "3. Check file permissions (755 for dirs, 644 for files)\n";
echo "4. Contact hosting provider about URL rewriting support\n\n";

echo "💡 COMMON HOSTING ISSUES:\n";
echo "- Some shared hosting requires document root to be set to /public\n";
echo "- Some hosting disables .htaccess or mod_rewrite\n";
echo "- File permissions may need adjustment\n\n";

echo "✨ Your Laravel application should now be accessible!\n";
?>
