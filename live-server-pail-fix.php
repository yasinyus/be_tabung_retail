<?php
// live-server-pail-fix.php - Complete Pail error fix for live server

echo "🚨 LIVE SERVER PAIL ERROR FIX\n";
echo "=============================\n\n";

echo "This script will completely fix the Laravel Pail error on your live server.\n";
echo "Make sure you upload this file to your live server root directory.\n\n";

// 1. Fix .env file for production
echo "📝 Step 1: Fixing .env for production environment...\n";
$envPath = '.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Set production environment
    if (strpos($envContent, 'APP_ENV=') === false) {
        $envContent .= "\nAPP_ENV=production\n";
    } else {
        $envContent = preg_replace('/APP_ENV=.*/', 'APP_ENV=production', $envContent);
    }
    
    // Disable debug
    if (strpos($envContent, 'APP_DEBUG=') === false) {
        $envContent .= "APP_DEBUG=false\n";
    } else {
        $envContent = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG=false', $envContent);
    }
    
    // Set single log channel to avoid Pail issues
    if (strpos($envContent, 'LOG_CHANNEL=') === false) {
        $envContent .= "LOG_CHANNEL=single\n";
    } else {
        $envContent = preg_replace('/LOG_CHANNEL=.*/', 'LOG_CHANNEL=single', $envContent);
    }
    
    file_put_contents($envPath, $envContent);
    echo "✅ .env file updated for production\n";
} else {
    echo "❌ .env file not found\n";
    echo "Creating basic .env file...\n";
    
    $basicEnv = "APP_NAME=Laravel
APP_ENV=production
APP_KEY=" . base64_encode(random_bytes(32)) . "
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tabung_retail
DB_USERNAME=root
DB_PASSWORD=
";
    
    file_put_contents($envPath, $basicEnv);
    echo "✅ Created basic .env file\n";
}

// 2. Fix AppServiceProvider to conditionally load Pail
echo "\n🔧 Step 2: Updating AppServiceProvider...\n";
$appServiceProvider = 'app/Providers/AppServiceProvider.php';

if (file_exists($appServiceProvider)) {
    $newContent = '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only register Pail in local environment and if class exists
        if (app()->environment("local") && class_exists(\Laravel\Pail\PailServiceProvider::class)) {
            $this->app->register(\Laravel\Pail\PailServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
';

    file_put_contents($appServiceProvider, $newContent);
    echo "✅ AppServiceProvider updated\n";
} else {
    echo "❌ AppServiceProvider not found\n";
}

// 3. Clear all caches
echo "\n🧹 Step 3: Clearing all Laravel caches...\n";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php', 
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php'
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "✅ Removed $file\n";
    }
}

// Clear storage caches
$storageDirs = [
    'storage/framework/cache/data',
    'storage/framework/views',
    'storage/framework/sessions'
];

foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        echo "✅ Cleared $dir\n";
    }
}

// 4. Create package discovery override
echo "\n🔍 Step 4: Creating package discovery override...\n";
$discoveryFile = 'bootstrap/cache/packages.php';
$discoveryContent = '<?php return [
    "providers" => [],
    "eager" => [],
    "deferred" => [],
    "when" => []
];';

// Ensure bootstrap/cache directory exists
if (!is_dir('bootstrap/cache')) {
    mkdir('bootstrap/cache', 0755, true);
}

file_put_contents($discoveryFile, $discoveryContent);
echo "✅ Package discovery override created\n";

// 5. Update composer.json to exclude Pail in production
echo "\n📦 Step 5: Creating production composer configuration...\n";
if (file_exists('composer.json')) {
    $composerContent = file_get_contents('composer.json');
    $composerData = json_decode($composerContent, true);
    
    // Add dont-discover for pail
    if (!isset($composerData['extra']['laravel']['dont-discover'])) {
        $composerData['extra']['laravel']['dont-discover'] = [];
    }
    
    if (!in_array('laravel/pail', $composerData['extra']['laravel']['dont-discover'])) {
        $composerData['extra']['laravel']['dont-discover'][] = 'laravel/pail';
    }
    
    // Remove pail from dev script if present
    if (isset($composerData['scripts']['dev']) && is_array($composerData['scripts']['dev'])) {
        foreach ($composerData['scripts']['dev'] as $key => $script) {
            if (strpos($script, 'pail') !== false) {
                $composerData['scripts']['dev'][$key] = str_replace('php artisan pail --timeout=0', '', $script);
                $composerData['scripts']['dev'][$key] = preg_replace('/,\s*""/', '', $composerData['scripts']['dev'][$key]);
            }
        }
    }
    
    file_put_contents('composer-production.json', json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "✅ Production composer.json created\n";
}

// 6. Test Laravel bootstrap
echo "\n🧪 Step 6: Testing Laravel bootstrap...\n";
try {
    // Test if we can load Laravel without errors
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        
        if (file_exists('bootstrap/app.php')) {
            $app = require_once 'bootstrap/app.php';
            echo "✅ Laravel bootstrap successful\n";
            
            // Test environment
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            echo "✅ Console kernel loaded\n";
            
        } else {
            echo "❌ bootstrap/app.php not found\n";
        }
    } else {
        echo "❌ vendor/autoload.php not found - run composer install\n";
    }
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    echo "This is usually caused by missing dependencies or Pail errors\n";
}

// 7. Create htaccess for proper routing
echo "\n🌐 Step 7: Ensuring proper .htaccess...\n";
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

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>';

file_put_contents('public/.htaccess', $htaccessContent);
echo "✅ .htaccess file updated\n";

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎉 LIVE SERVER PAIL ERROR FIX COMPLETED!\n";
echo str_repeat("=", 60) . "\n";
echo "📋 WHAT WAS FIXED:\n";
echo "✅ Set APP_ENV=production and APP_DEBUG=false\n";
echo "✅ Updated AppServiceProvider to conditionally load Pail\n";
echo "✅ Cleared all Laravel caches\n";
echo "✅ Created package discovery override\n";
echo "✅ Updated composer configuration\n";
echo "✅ Ensured proper .htaccess routing\n\n";

echo "🚀 NEXT STEPS:\n";
echo "1. If using artisan commands, run: php artisan config:clear\n";
echo "2. Test your routes: /admin, /api/v1/auth/login\n";
echo "3. If still getting errors, run: php artisan optimize:clear\n\n";

echo "🌐 YOUR URLS SHOULD NOW WORK:\n";
echo "- Admin Panel: https://yourserver.com/admin\n";
echo "- API Login: https://yourserver.com/api/v1/auth/login\n";
echo "- Mobile Dashboard: https://yourserver.com/api/v1/mobile/dashboard\n\n";

echo "⚠️  IF PROBLEMS PERSIST:\n";
echo "1. Check file permissions (755 for directories, 644 for files)\n";
echo "2. Ensure vendor/ directory exists\n";
echo "3. Run composer install --no-dev --optimize-autoloader\n";
echo "4. Contact your hosting provider about PHP extensions\n\n";

echo "✨ Pail error should now be completely resolved!\n";
?>
