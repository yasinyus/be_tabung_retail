<?php

echo "=== SYNC CONFIGURATION TO SERVER ===\n\n";

echo "🎉 LOCAL WORKING - Now syncing to server...\n\n";

// Create deployment checklist
echo "1️⃣  FILES TO UPLOAD TO SERVER:\n";
echo "   ✅ app/Providers/Filament/AdminPanelProvider.php\n";
echo "   ✅ app/Models/User.php\n";
echo "   ✅ All Filament Resource files\n";
echo "   ✅ .env file (if modified)\n";

echo "\n2️⃣  COMMANDS TO RUN ON SERVER:\n";
echo "   composer dump-autoload --optimize\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan cache:clear\n";
echo "   php artisan view:clear\n";
echo "   php artisan optimize:clear\n";

echo "\n3️⃣  SERVER-SPECIFIC CHECKS:\n";
echo "   - Check file permissions\n";
echo "   - Check web server configuration\n";
echo "   - Check .htaccess file\n";
echo "   - Restart web server\n";

echo "\n4️⃣  CREATE SERVER SYNC SCRIPT:\n";

// Create quick server commands
$serverCommands = '#!/bin/bash
echo "=== SERVER SYNC COMMANDS ==="

echo "1. Optimizing autoloader..."
composer dump-autoload --optimize

echo "2. Clearing all caches..."
php artisan config:clear
php artisan route:clear  
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

echo "3. Setting permissions..."
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

echo "4. Checking Filament..."
php artisan route:list | grep admin

echo "✅ Server sync complete!"
echo "Test: http://your-domain/admin"
';

file_put_contents('server_sync.sh', $serverCommands);
echo "   ✅ Created server_sync.sh\n";

// Create verification script for server
$serverVerify = '<?php
// Server verification script
echo "=== SERVER VERIFICATION ===\n";

try {
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "✅ Laravel bootstrap: OK\n";
    
    // Check Filament
    $panels = \Filament\Facades\Filament::getPanels();
    echo "✅ Filament panels: " . count($panels) . "\n";
    
    // Check routes
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = 0;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), "admin")) {
            $adminRoutes++;
        }
    }
    echo "✅ Admin routes: {$adminRoutes}\n";
    
    // Check users
    $users = \App\Models\User::count();
    echo "✅ Users: {$users}\n";
    
    // Check admin user
    $admin = \App\Models\User::where("email", "admin@ptgas.com")->first();
    if ($admin) {
        echo "✅ Admin user exists: {$admin->name}\n";
        echo "✅ Can access panel: " . ($admin->canAccessPanel(null) ? "YES" : "NO") . "\n";
    }
    
    echo "\n🎯 Server appears ready!\n";
    echo "Try: http://your-domain/admin\n";
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}
?>';

file_put_contents('server_verify.php', $serverVerify);
echo "   ✅ Created server_verify.php\n";

echo "\n🚀 DEPLOYMENT STEPS:\n";
echo "\nSTEP 1 - Upload these files to server:\n";
echo "   - app/Providers/Filament/AdminPanelProvider.php\n";
echo "   - app/Models/User.php\n";
echo "   - server_sync.sh\n";
echo "   - server_verify.php\n";

echo "\nSTEP 2 - Run on server:\n";
echo "   chmod +x server_sync.sh\n";
echo "   ./server_sync.sh\n";

echo "\nSTEP 3 - Verify on server:\n";
echo "   php server_verify.php\n";

echo "\nSTEP 4 - Test:\n";
echo "   http://8.215.70.68/admin\n";

echo "\n⚠️  IF STILL 403 AFTER SYNC:\n";
echo "The issue might be:\n";
echo "- Web server virtual host configuration\n";
echo "- File ownership (chown -R www-data:www-data .)\n";
echo "- SELinux or server security policies\n";
echo "- Apache/Nginx specific configuration\n";

echo "\n💡 QUICK TEST:\n";
echo "Upload server_verify.php and run it first to confirm Laravel is working on server.\n";

?>
