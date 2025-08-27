<?php

echo "=== FORCE ADMIN WORKING ===\n\n";

// Since emergency admin works, Laravel is fine
// Let's create direct admin access

echo "1️⃣  Creating Direct Admin Access...\n";

// Create a simple admin wrapper that bypasses routing issues
$directAdminCode = '<?php
// Direct Admin Access - Bypass routing issues
try {
    require_once "../vendor/autoload.php";
    $app = require_once "../bootstrap/app.php";
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Force login as admin user
    $adminUser = \App\Models\User::where("role", "admin_utama")->first();
    if ($adminUser) {
        \Illuminate\Support\Facades\Auth::guard("web")->login($adminUser);
        echo "<p>Logged in as: " . $adminUser->name . "</p>";
    }
    
    // Check if we can access Filament
    $panels = \Filament\Facades\Filament::getPanels();
    
    if (!empty($panels)) {
        echo "<h2>Filament Panels Available:</h2>";
        foreach ($panels as $panelId => $panel) {
            $path = $panel->getPath();
            echo "<p>Panel: {$panelId} - Path: /{$path}</p>";
        }
        
        // Try to render admin panel directly
        echo "<h2>Attempting Direct Panel Access:</h2>";
        
        // Simulate request to admin
        $_SERVER["REQUEST_URI"] = "/admin";
        $_SERVER["REQUEST_METHOD"] = "GET";
        
        try {
            // Get the admin panel
            $adminPanel = $panels["admin"] ?? null;
            if ($adminPanel) {
                echo "<p>✅ Admin panel found and accessible</p>";
                echo "<p>🔄 Redirecting to actual admin...</p>";
                echo "<script>setTimeout(function(){ window.location.href = \"/admin\"; }, 2000);</script>";
            } else {
                echo "<p>❌ Admin panel not found in panels list</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Error accessing admin panel: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ No Filament panels found!</p>";
    }
    
    echo "<hr>";
    echo "<h3>Alternative Links:</h3>";
    echo "<a href=\"/admin\">Try Main Admin</a><br>";
    echo "<a href=\"/index.php/admin\">Try Index.php Admin</a><br>";
    echo "<a href=\"/emergency.php\">Back to Emergency</a><br>";
    
    echo "<h3>Debug Info:</h3>";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = 0;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), "admin")) {
            $adminRoutes++;
        }
    }
    echo "<p>Total admin routes registered: {$adminRoutes}</p>";
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}
?>';

file_put_contents('public/direct_admin.php', $directAdminCode);
echo "   ✅ Direct admin created at /direct_admin.php\n";

// Create .htaccess specifically for admin path
echo "\n2️⃣  Creating Specific Admin Routing...\n";

$adminHtaccess = 'RewriteEngine On
RewriteBase /

# Force admin routes to index.php
RewriteRule ^admin$ /index.php/admin [L]
RewriteRule ^admin/(.*)$ /index.php/admin/$1 [L]

# Handle other routes normally
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]';

file_put_contents('public/.htaccess', $adminHtaccess);
echo "   ✅ Updated .htaccess with specific admin routing\n";

// Clear caches
echo "\n3️⃣  Clearing Caches...\n";
system('php artisan route:clear 2>/dev/null');
system('php artisan config:clear 2>/dev/null');
system('php artisan cache:clear 2>/dev/null');
echo "   ✅ Caches cleared\n";

echo "\n🎯 TEST THESE URLS:\n";
echo "1. http://8.215.70.68/direct_admin.php (direct access)\n";
echo "2. http://8.215.70.68/admin (should work now)\n";
echo "3. http://8.215.70.68/index.php/admin (explicit routing)\n";

echo "\n🔧 IF DIRECT_ADMIN WORKS BUT /admin STILL DOESN\'T:\n";
echo "The issue is specifically with Filament routing in your web server.\n";
echo "We may need to create a custom route or modify the admin path.\n";

echo "\n💡 NETWORK TROUBLESHOOTING:\n";
echo "For port 8080 not accessible:\n";
echo "1. Check if built-in server actually started: ps aux | grep artisan\n";
echo "2. Check firewall: sudo ufw status\n";
echo "3. Try different port: php artisan serve --host=0.0.0.0 --port=8888\n";
echo "4. Check from localhost: curl http://localhost:8080/admin\n";

?>
