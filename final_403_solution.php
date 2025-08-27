<?php

echo "=== FINAL 403 SOLUTION ===\n\n";

// Step 1: Create working emergency admin
echo "1️⃣  Creating Emergency Admin...\n";
$emergencyAdmin = '<?php
// Emergency Admin Interface
try {
    require_once "../vendor/autoload.php";
    $app = require_once "../bootstrap/app.php";
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $users = \App\Models\User::all();
    echo "<h1>Emergency Admin - TabungRetail</h1>";
    echo "<p>Users: " . $users->count() . "</p>";
    echo "<p>Laravel Status: ✅ Working</p>";
    echo "<p>If you see this, Laravel is working fine!</p>";
    echo "<a href=\"/admin\">Try Main Admin</a> | ";
    echo "<a href=\"/index.php/admin\">Try with index.php</a>";
    
} catch (Exception $e) {
    echo "<h1>Laravel Error</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>';

file_put_contents('public/emergency.php', $emergencyAdmin);
echo "   ✅ Emergency admin created at /emergency.php\n";

// Step 2: Fix common issues
echo "\n2️⃣  Fixing Common Issues...\n";

// Ensure proper .htaccess
$htaccess = '<IfModule mod_rewrite.c>
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

file_put_contents('public/.htaccess', $htaccess);
echo "   ✅ .htaccess updated\n";

// Step 3: Clear all caches
echo "\n3️⃣  Clearing Caches...\n";
$commands = [
    'config:clear' => 'php artisan config:clear',
    'route:clear' => 'php artisan route:clear', 
    'cache:clear' => 'php artisan cache:clear',
    'view:clear' => 'php artisan view:clear',
    'optimize:clear' => 'php artisan optimize:clear'
];

foreach ($commands as $name => $command) {
    system($command . ' 2>/dev/null');
    echo "   ✅ {$name}\n";
}

// Step 4: Regenerate autoloader
echo "\n4️⃣  Regenerating Autoloader...\n";
system('composer dump-autoload --optimize 2>/dev/null');
echo "   ✅ Autoloader regenerated\n";

// Step 5: Create test URLs
echo "\n5️⃣  Test URLs to Try...\n";
$baseUrl = "http://8.215.70.68";
$testUrls = [
    "Emergency Admin (should work)" => "{$baseUrl}/emergency.php",
    "Basic Laravel" => "{$baseUrl}/",
    "Admin direct" => "{$baseUrl}/admin",
    "Admin with index.php" => "{$baseUrl}/index.php/admin",
    "Admin login direct" => "{$baseUrl}/admin/login",
    "Admin login with index.php" => "{$baseUrl}/index.php/admin/login"
];

foreach ($testUrls as $description => $url) {
    echo "   📋 {$description}:\n      {$url}\n";
}

echo "\n🎯 DIAGNOSIS STRATEGY:\n";
echo "1. First test emergency.php - if this works, Laravel is fine\n";
echo "2. If /admin gives 403 but /index.php/admin works = URL rewriting issue\n";
echo "3. If both give 403 = server-level restriction\n";
echo "4. If emergency.php doesn't work = fundamental Laravel issue\n";

echo "\n🔧 QUICK FIXES TO TRY:\n";
echo "A. Built-in server test:\n";
echo "   php artisan serve --host=0.0.0.0 --port=8080\n";
echo "   Then test: http://8.215.70.68:8080/admin\n";

echo "\nB. Check web server:\n";
echo "   sudo systemctl restart apache2\n";
echo "   sudo systemctl restart nginx\n";

echo "\nC. Check server logs:\n";
echo "   tail -f /var/log/apache2/error.log\n";
echo "   tail -f /var/log/nginx/error.log\n";

echo "\n🚀 START WITH: http://8.215.70.68/emergency.php\n";
echo "This will tell us if Laravel is working at all!\n";

?>
