<?php
/**
 * FIX 404 ADMIN - Script untuk memperbaiki routing dan admin panel
 * Upload dan jalankan untuk fix masalah 404 admin
 */

echo "🔧 FIX 404 ADMIN - Starting...\n\n";

// 1. CLEAR ROUTE CACHE
echo "🗑️ Step 1: Clearing route cache...\n";
$routeCacheFiles = [
    'bootstrap/cache/routes-v7.php',
    'bootstrap/cache/routes.php'
];

foreach ($routeCacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "✅ Deleted: $file\n";
    }
}

// 2. RECREATE WEB.PHP ROUTES
echo "\n🌐 Step 2: Checking web routes...\n";
$webRoutesPath = 'routes/web.php';
if (file_exists($webRoutesPath)) {
    $webRoutes = file_get_contents($webRoutesPath);
    echo "✅ web.php exists\n";
    
    // Check if it has basic content
    if (strlen($webRoutes) < 100) {
        echo "⚠️ web.php seems empty, recreating...\n";
        $basicWebRoutes = '<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

// Redirect /admin to /admin/login  
Route::redirect("/admin", "/admin/login");
';
        file_put_contents($webRoutesPath, $basicWebRoutes);
        echo "✅ Recreated web.php\n";
    }
} else {
    echo "❌ web.php missing, creating basic one...\n";
    $basicWebRoutes = '<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::redirect("/admin", "/admin/login");
';
    file_put_contents($webRoutesPath, $basicWebRoutes);
    echo "✅ Created web.php\n";
}

// 3. CHECK FILAMENT INSTALLATION
echo "\n📋 Step 3: Checking Filament installation...\n";
$filamentPaths = [
    'vendor/filament',
    'app/Providers/Filament',
    'config/filament.php'
];

$filamentInstalled = false;
foreach ($filamentPaths as $path) {
    if (file_exists($path)) {
        echo "✅ Found: $path\n";
        $filamentInstalled = true;
        break;
    }
}

if (!$filamentInstalled) {
    echo "❌ Filament not detected\n";
    echo "💡 You may need to install Filament or check if admin routes are different\n";
}

// 4. CREATE BASIC WELCOME VIEW
echo "\n📄 Step 4: Ensuring welcome view exists...\n";
$welcomeViewPath = 'resources/views/welcome.blade.php';
if (!file_exists($welcomeViewPath)) {
    $welcomeView = '<!DOCTYPE html>
<html>
<head>
    <title>{{ config("app.name") }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 600px; margin: 0 auto; text-align: center; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Laravel Application</h1>
        <p>Your Laravel application is running successfully!</p>
        <hr>
        <h3>Quick Links:</h3>
        <p>
            <a href="/admin" class="btn">Admin Panel</a>
            <a href="/admin/login" class="btn">Admin Login</a>
        </p>
        <hr>
        <p><strong>Time:</strong> {{ date("Y-m-d H:i:s") }}</p>
    </div>
</body>
</html>';

    // Ensure views directory exists
    if (!is_dir('resources/views')) {
        mkdir('resources/views', 0755, true);
    }
    
    file_put_contents($welcomeViewPath, $welcomeView);
    echo "✅ Created welcome view\n";
} else {
    echo "✅ Welcome view exists\n";
}

// 5. CREATE .HTACCESS FOR PROPER ROUTING
echo "\n🌐 Step 5: Creating proper .htaccess...\n";
$htaccessContent = '<IfModule mod_rewrite.c>
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

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>';

file_put_contents('.htaccess', $htaccessContent);
echo "✅ Created .htaccess\n";

// 6. CHECK COMPOSER AUTOLOAD
echo "\n📦 Step 6: Checking composer autoload...\n";
if (file_exists('vendor/autoload.php')) {
    echo "✅ Composer autoload exists\n";
} else {
    echo "❌ Composer autoload missing!\n";
    echo "💡 You need to run: composer install\n";
}

// 7. CREATE TEST ROUTES FILE
echo "\n🧪 Step 7: Creating route test file...\n";
$testRoutes = '<?php
// Test routes - check if Laravel routing works
echo "<h1>🧪 Laravel Route Test</h1>";
echo "<hr>";

try {
    // Test if Laravel is properly loaded
    if (function_exists("app")) {
        echo "<h2>✅ Laravel App Loaded</h2>";
        
        // Test route resolution
        echo "<h3>Route Tests:</h3>";
        echo "<ul>";
        echo "<li><a href=\"/\">Home Page (/)</a></li>";
        echo "<li><a href=\"/admin\">Admin Panel (/admin)</a></li>";
        echo "<li><a href=\"/admin/login\">Admin Login (/admin/login)</a></li>";
        echo "<li><a href=\"/api/v1/auth/login\">API Login (/api/v1/auth/login)</a></li>";
        echo "</ul>";
        
    } else {
        echo "<h2>❌ Laravel Not Loaded</h2>";
        echo "<p>Laravel framework is not properly loaded</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Time:</strong> " . date("Y-m-d H:i:s") . "</p>";
?>';

file_put_contents('test-routes.php', $testRoutes);
echo "✅ Created test-routes.php\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 FIX 404 ADMIN COMPLETED!\n";
echo str_repeat("=", 50) . "\n\n";

echo "✅ What was fixed:\n";
echo "   - Cleared route cache\n";
echo "   - Ensured web.php routes exist\n";
echo "   - Created welcome view\n";
echo "   - Fixed .htaccess for proper routing\n";
echo "   - Created route test file\n\n";

echo "🧪 Test your routes:\n";
echo "   1. Home: https://test.gasalamsolusi.my.id/\n";
echo "   2. Route test: https://test.gasalamsolusi.my.id/test-routes.php\n";
echo "   3. Admin: https://test.gasalamsolusi.my.id/admin\n";
echo "   4. Admin login: https://test.gasalamsolusi.my.id/admin/login\n\n";

echo "🚨 If admin still 404:\n";
echo "   - Check if Filament is installed\n";
echo "   - Check app/Providers for FilamentServiceProvider\n";
echo "   - Run: composer install (if vendor missing)\n";
echo "   - Contact hosting to run: php artisan route:list\n\n";

echo "🗑️ Delete after testing:\n";
echo "   - FIX_404_ADMIN.php\n";
echo "   - test-routes.php\n\n";
?>
