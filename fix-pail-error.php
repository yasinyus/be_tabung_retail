<?php
// fix-pail-error.php - Fix Laravel Pail error for live server

echo "🔧 Fixing Laravel Pail error...\n";

// 1. Check if vendor/laravel/pail exists
$pailPath = 'vendor/laravel/pail';
if (!is_dir($pailPath)) {
    echo "❌ Laravel Pail not installed in vendor directory\n";
    echo "💡 This is normal for production servers with --no-dev\n";
} else {
    echo "✅ Laravel Pail found in vendor\n";
}

// 2. Create temporary service provider fix
$appConfigPath = 'config/app.php';
if (file_exists($appConfigPath)) {
    $content = file_get_contents($appConfigPath);
    
    // Check if Pail is mentioned
    if (strpos($content, 'Pail') !== false) {
        echo "⚠️  Pail found in config/app.php\n";
        // We'll handle this in the next step
    } else {
        echo "✅ No Pail reference in config/app.php\n";
    }
}

// 3. Check bootstrap/app.php for Laravel 11
$bootstrapPath = 'bootstrap/app.php';
if (file_exists($bootstrapPath)) {
    $content = file_get_contents($bootstrapPath);
    echo "✅ Bootstrap app.php exists\n";
}

// 4. Create environment-aware solution
$envContent = "# Add this to .env for live server\n";
$envContent .= "APP_ENV=production\n";
$envContent .= "APP_DEBUG=false\n";
$envContent .= "LOG_LEVEL=error\n";

file_put_contents('.env.live-server', $envContent);
echo "✅ Created .env.live-server with production settings\n";

// 5. Create conditional service provider
$conditionalProvider = '<?php
// Add this to AppServiceProvider if needed

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Only register Pail in local environment
        if ($this->app->environment("local")) {
            if (class_exists(\Laravel\Pail\PailServiceProvider::class)) {
                $this->app->register(\Laravel\Pail\PailServiceProvider::class);
            }
        }
    }

    public function boot()
    {
        //
    }
}
';

file_put_contents('conditional-pail-provider.php', $conditionalProvider);
echo "✅ Created conditional provider example\n";

// 6. Alternative: Remove from composer.json for production
echo "\n🔧 Recommended Solutions:\n";
echo "1. Run composer install --no-dev on live server\n";
echo "2. Or add this to composer.json scripts:\n";
echo '   "post-install-cmd": ["php artisan optimize:clear || true"]\n';
echo "3. Or set APP_ENV=production in live server .env\n";

echo "\n✅ Fix script completed!\n";
echo "🌐 Upload .env.live-server to server and rename to .env\n";
echo "🎯 Or ensure APP_ENV=production on live server\n";
?>
