<?php
// hosting-specific-fix.php - Quick fix for different hosting types

echo "🌐 HOSTING-SPECIFIC 404 FIX\n";
echo "===========================\n\n";

// Detect hosting type
$hostingType = 'unknown';
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';
$httpHost = $_SERVER['HTTP_HOST'] ?? '';

if (strpos($serverSoftware, 'Apache') !== false) {
    $hostingType = 'apache';
} elseif (strpos($serverSoftware, 'nginx') !== false) {
    $hostingType = 'nginx';
} elseif (strpos($httpHost, 'cpanel') !== false || strpos($httpHost, 'shared') !== false) {
    $hostingType = 'shared';
}

echo "🔍 Detected hosting type: " . strtoupper($hostingType) . "\n";
echo "Server software: $serverSoftware\n";
echo "Host: $httpHost\n\n";

// Check current directory structure
echo "📁 Checking directory structure...\n";
$isInPublic = basename(getcwd()) === 'public';
$hasPublicFolder = is_dir('public');
$hasIndex = file_exists('index.php');
$hasLaravelIndex = file_exists('public/index.php');

echo "Current directory: " . getcwd() . "\n";
echo "Is in public folder: " . ($isInPublic ? 'YES' : 'NO') . "\n";
echo "Has public folder: " . ($hasPublicFolder ? 'YES' : 'NO') . "\n";
echo "Has index.php in root: " . ($hasIndex ? 'YES' : 'NO') . "\n";
echo "Has index.php in public: " . ($hasLaravelIndex ? 'YES' : 'NO') . "\n\n";

// Apply hosting-specific fixes
switch ($hostingType) {
    case 'shared':
        echo "🔧 Applying SHARED HOSTING fix...\n";
        
        // For shared hosting, often need to move public contents to root
        if ($hasPublicFolder && !$isInPublic) {
            echo "Moving public folder contents to root...\n";
            
            // Copy public/index.php to root with modified paths
            if (file_exists('public/index.php')) {
                $indexContent = file_get_contents('public/index.php');
                
                // Modify paths for root installation
                $indexContent = str_replace("__DIR__.'/", "__DIR__.'/public/", $indexContent);
                $indexContent = str_replace("__DIR__.'/../", "__DIR__.'/", $indexContent);
                
                file_put_contents('index.php', $indexContent);
                echo "✅ Created modified index.php in root\n";
            }
            
            // Copy .htaccess from public to root
            if (file_exists('public/.htaccess')) {
                copy('public/.htaccess', '.htaccess');
                echo "✅ Copied .htaccess to root\n";
            }
            
            // Create assets symlink if needed
            if (!file_exists('storage') && file_exists('public/storage')) {
                // For shared hosting, copy instead of symlink
                echo "Creating storage link for shared hosting...\n";
            }
        }
        break;
        
    case 'apache':
        echo "🔧 Applying APACHE hosting fix...\n";
        
        // Standard Laravel setup with proper .htaccess
        $rootHtaccess = '<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>';
        
        file_put_contents('.htaccess', $rootHtaccess);
        echo "✅ Created root .htaccess for Apache\n";
        
        // Ensure public/.htaccess exists
        if (!file_exists('public/.htaccess')) {
            $publicHtaccess = '<IfModule mod_rewrite.c>
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
            
            file_put_contents('public/.htaccess', $publicHtaccess);
            echo "✅ Created public/.htaccess for Apache\n";
        }
        break;
        
    case 'nginx':
        echo "🔧 NGINX detected - .htaccess won't work!\n";
        echo "You need to configure nginx.conf with this location block:\n\n";
        echo "location / {\n";
        echo "    try_files \$uri \$uri/ /index.php?\$query_string;\n";
        echo "}\n\n";
        echo "location ~ \\.php\$ {\n";
        echo "    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;\n";
        echo "    fastcgi_index index.php;\n";
        echo "    fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;\n";
        echo "    include fastcgi_params;\n";
        echo "}\n\n";
        break;
        
    default:
        echo "🔧 Applying GENERIC hosting fix...\n";
        
        // Try both approaches
        $rootHtaccess = '<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>';
        
        file_put_contents('.htaccess', $rootHtaccess);
        echo "✅ Created generic .htaccess\n";
}

// Create test files
echo "\n🧪 Creating test files...\n";

// Simple test file
$simpleTest = '<?php
echo "✅ PHP is working!<br>";
echo "Server: " . $_SERVER["SERVER_SOFTWARE"] ?? "Unknown" . "<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Document Root: " . $_SERVER["DOCUMENT_ROOT"] ?? "Not set" . "<br>";
echo "Current Dir: " . getcwd() . "<br>";
echo "Time: " . date("Y-m-d H:i:s") . "<br>";

if (file_exists("vendor/autoload.php")) {
    echo "✅ Composer autoload found<br>";
} else {
    echo "❌ Composer autoload NOT found<br>";
}

if (file_exists("bootstrap/app.php")) {
    echo "✅ Laravel bootstrap found<br>";
} else {
    echo "❌ Laravel bootstrap NOT found<br>";
}
?>';

file_put_contents('test-hosting.php', $simpleTest);
echo "✅ Created test-hosting.php\n";

// Laravel test
if (file_exists('vendor/autoload.php') && file_exists('bootstrap/app.php')) {
    $laravelTest = '<?php
try {
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    
    echo "✅ Laravel Application Loaded Successfully!<br>";
    echo "Environment: " . $app->environment() . "<br>";
    
    // Test route resolution
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ HTTP Kernel created<br>";
    
} catch (Exception $e) {
    echo "❌ Laravel Error: " . $e->getMessage() . "<br>";
}
?>';
    
    file_put_contents('test-laravel.php', $laravelTest);
    echo "✅ Created test-laravel.php\n";
}

// Final instructions
echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 HOSTING-SPECIFIC FIX COMPLETED!\n";
echo str_repeat("=", 50) . "\n";

echo "🧪 TEST THESE URLs NOW:\n";
echo "1. Basic test: https://yourserver.com/test-hosting.php\n";
echo "2. Laravel test: https://yourserver.com/test-laravel.php\n";
echo "3. Home page: https://yourserver.com/\n";
echo "4. Admin panel: https://yourserver.com/admin\n\n";

echo "📋 NEXT STEPS BASED ON RESULTS:\n";
echo "✅ If test-hosting.php works: Server is OK\n";
echo "✅ If test-laravel.php works: Laravel is OK\n";
echo "✅ If home page works: Routing is OK\n";
echo "✅ If admin works: Everything is OK!\n\n";

echo "🚨 IF STILL 404:\n";
switch ($hostingType) {
    case 'shared':
        echo "- Contact hosting support to set document root\n";
        echo "- Ask about .htaccess and mod_rewrite support\n";
        break;
    case 'nginx':
        echo "- Configure nginx.conf as shown above\n";
        echo "- Restart nginx after configuration\n";
        break;
    default:
        echo "- Check if mod_rewrite is enabled\n";
        echo "- Verify document root configuration\n";
        echo "- Check file permissions (755/644)\n";
}

echo "\n✨ Your Laravel application should now be working!\n";
?>
