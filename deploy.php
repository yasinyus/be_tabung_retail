<?php
/**
 * Laravel Deployment Script for Shared Hosting
 * Usage: Upload this file to your hosting and run via browser
 * URL: https://yourdomain.com/deploy.php
 * 
 * ⚠️ SECURITY: Delete this file after deployment!
 */

// Basic authentication (change credentials!)
$auth_user = 'admin';
$auth_pass = 'deploy123';

// Simple auth check
if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] !== $auth_user || 
    $_SERVER['PHP_AUTH_PW'] !== $auth_pass) {
    header('WWW-Authenticate: Basic realm="Deployment"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access Denied';
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Deployment - Tabung Retail</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .step { margin: 10px 0; padding: 10px; border-left: 4px solid #3498db; background: #ecf0f1; }
        .success { border-color: #2ecc71; background: #d5f4e6; }
        .error { border-color: #e74c3c; background: #fadbd8; }
        .warning { border-color: #f39c12; background: #fdf2e9; }
        .command { background: #2c3e50; color: #ecf0f1; padding: 10px; border-radius: 4px; font-family: monospace; }
        pre { white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Laravel Deployment - Tabung Retail</h1>
        <p><strong>Environment:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
        <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
        <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            deploy();
        } else {
            showForm();
        }
        
        function showForm() {
        ?>
            <form method="POST" onsubmit="return confirm('Are you sure you want to deploy?');">
                <div class="step warning">
                    <h3>⚠️ Pre-deployment Checklist:</h3>
                    <ul>
                        <li>✅ Laravel files uploaded to correct directories</li>
                        <li>✅ Database created and credentials configured</li>
                        <li>✅ .env file updated with production settings</li>
                        <li>✅ File permissions set correctly (755/775)</li>
                        <li>✅ Composer dependencies uploaded/installed</li>
                    </ul>
                </div>
                
                <button type="submit" style="background: #3498db; color: white; padding: 15px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
                    🚀 Start Deployment
                </button>
            </form>
        <?php
        }
        
        function deploy() {
            echo '<h2>🔧 Deployment Process</h2>';
            
            // Check if Laravel is properly installed
            if (!file_exists('vendor/autoload.php')) {
                echo '<div class="step error">❌ Error: vendor/autoload.php not found. Please upload vendor folder or run composer install.</div>';
                return;
            }
            
            try {
                require_once 'vendor/autoload.php';
                $app = require_once 'bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                
                $steps = [
                    'config:clear' => '🧹 Clearing configuration cache',
                    'cache:clear' => '🧹 Clearing application cache', 
                    'route:clear' => '🧹 Clearing route cache',
                    'view:clear' => '🧹 Clearing view cache',
                    'config:cache' => '⚡ Caching configuration',
                    'route:cache' => '⚡ Caching routes',
                    'view:cache' => '⚡ Caching views',
                ];
                
                foreach ($steps as $command => $description) {
                    echo "<div class='step'>";
                    echo "<strong>$description</strong><br>";
                    echo "<div class='command'>php artisan $command</div>";
                    
                    try {
                        ob_start();
                        $exitCode = $kernel->call($command);
                        $output = ob_get_clean();
                        
                        if ($exitCode === 0) {
                            echo "<span style='color: #2ecc71;'>✅ Success</span>";
                        } else {
                            echo "<span style='color: #e74c3c;'>❌ Failed (Exit code: $exitCode)</span>";
                        }
                        
                        if ($output) {
                            echo "<pre>$output</pre>";
                        }
                    } catch (Exception $e) {
                        echo "<span style='color: #e74c3c;'>❌ Error: " . $e->getMessage() . "</span>";
                    }
                    echo "</div>";
                }
                
                // Test database connection
                echo "<div class='step'>";
                echo "<strong>🗄️ Testing database connection</strong><br>";
                try {
                    $pdo = new PDO(
                        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
                        env('DB_USERNAME'),
                        env('DB_PASSWORD')
                    );
                    echo "<span style='color: #2ecc71;'>✅ Database connection successful</span>";
                } catch (Exception $e) {
                    echo "<span style='color: #e74c3c;'>❌ Database connection failed: " . $e->getMessage() . "</span>";
                }
                echo "</div>";
                
                // Run migrations
                echo "<div class='step'>";
                echo "<strong>🗄️ Running database migrations</strong><br>";
                echo "<div class='command'>php artisan migrate --force</div>";
                try {
                    ob_start();
                    $exitCode = $kernel->call('migrate', ['--force' => true]);
                    $output = ob_get_clean();
                    
                    if ($exitCode === 0) {
                        echo "<span style='color: #2ecc71;'>✅ Migrations completed</span>";
                    } else {
                        echo "<span style='color: #e74c3c;'>❌ Migrations failed</span>";
                    }
                    
                    if ($output) {
                        echo "<pre>$output</pre>";
                    }
                } catch (Exception $e) {
                    echo "<span style='color: #e74c3c;'>❌ Error: " . $e->getMessage() . "</span>";
                }
                echo "</div>";
                
                // Create storage symlink
                echo "<div class='step'>";
                echo "<strong>🔗 Creating storage symlink</strong><br>";
                echo "<div class='command'>php artisan storage:link</div>";
                try {
                    ob_start();
                    $exitCode = $kernel->call('storage:link');
                    $output = ob_get_clean();
                    
                    if ($exitCode === 0) {
                        echo "<span style='color: #2ecc71;'>✅ Storage symlink created</span>";
                    } else {
                        echo "<span style='color: #f39c12;'>⚠️ Storage symlink may already exist</span>";
                    }
                    
                    if ($output) {
                        echo "<pre>$output</pre>";
                    }
                } catch (Exception $e) {
                    echo "<span style='color: #e74c3c;'>❌ Error: " . $e->getMessage() . "</span>";
                }
                echo "</div>";
                
                // System information
                echo "<div class='step success'>";
                echo "<h3>📊 System Information</h3>";
                echo "<ul>";
                echo "<li><strong>Laravel Version:</strong> " . app()->version() . "</li>";
                echo "<li><strong>PHP Version:</strong> " . PHP_VERSION . "</li>";
                echo "<li><strong>Environment:</strong> " . app()->environment() . "</li>";
                echo "<li><strong>Debug Mode:</strong> " . (config('app.debug') ? 'Enabled' : 'Disabled') . "</li>";
                echo "<li><strong>App URL:</strong> " . config('app.url') . "</li>";
                echo "<li><strong>Database:</strong> " . config('database.default') . "</li>";
                echo "<li><strong>Cache Driver:</strong> " . config('cache.default') . "</li>";
                echo "<li><strong>Session Driver:</strong> " . config('session.driver') . "</li>";
                echo "</ul>";
                echo "</div>";
                
                // Test API endpoints
                echo "<div class='step'>";
                echo "<strong>🧪 Testing API endpoints</strong><br>";
                $testUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api/test';
                echo "Testing: <a href='$testUrl' target='_blank'>$testUrl</a><br>";
                
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'ignore_errors' => true
                    ]
                ]);
                
                $response = @file_get_contents($testUrl, false, $context);
                if ($response) {
                    echo "<span style='color: #2ecc71;'>✅ API endpoint accessible</span>";
                } else {
                    echo "<span style='color: #e74c3c;'>❌ API endpoint not accessible</span>";
                }
                echo "</div>";
                
                echo "<div class='step success'>";
                echo "<h3>🎉 Deployment Completed!</h3>";
                echo "<p>Your Laravel application should now be running on production.</p>";
                echo "<p><strong>Important:</strong> Please delete this deploy.php file for security!</p>";
                echo "<p><strong>Next steps:</strong></p>";
                echo "<ul>";
                echo "<li>Test your application: <a href='/' target='_blank'>Homepage</a></li>";
                echo "<li>Test admin panel: <a href='/admin' target='_blank'>Admin Panel</a></li>";
                echo "<li>Test API: <a href='/api/test' target='_blank'>API Test</a></li>";
                echo "<li>Setup SSL certificate (recommended)</li>";
                echo "<li>Configure backup strategy</li>";
                echo "</ul>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='step error'>";
                echo "<h3>❌ Deployment Failed</h3>";
                echo "<p>Error: " . $e->getMessage() . "</p>";
                echo "<p>Please check your Laravel installation and try again.</p>";
                echo "</div>";
            }
            
            // Self-destruct option
            echo '<hr>';
            echo '<form method="POST" onsubmit="return confirm(\'This will delete deploy.php permanently. Continue?\');">';
            echo '<input type="hidden" name="self_destruct" value="1">';
            echo '<button type="submit" style="background: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">';
            echo '🗑️ Delete deploy.php (Recommended for security)';
            echo '</button>';
            echo '</form>';
        }
        
        // Handle self-destruct
        if (isset($_POST['self_destruct'])) {
            if (unlink(__FILE__)) {
                echo '<div class="step success">✅ deploy.php has been deleted successfully!</div>';
                echo '<script>setTimeout(function(){ window.location.href = "/"; }, 3000);</script>';
            } else {
                echo '<div class="step error">❌ Failed to delete deploy.php. Please delete manually.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
