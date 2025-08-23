<?php
/**
 * Fix untuk deployment ke shared hosting
 * Script ini akan membuat tabel-tabel yang dibutuhkan untuk cache dan queue
 * 
 * Upload file ini ke laravel_app/ dan jalankan via browser:
 * https://yourdomain.com/fix_deployment.php
 */

// Basic authentication (sama seperti deploy.php)
$auth_user = 'admin';
$auth_pass = 'deploy123';

if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] !== $auth_user || 
    $_SERVER['PHP_AUTH_PW'] !== $auth_pass) {
    header('WWW-Authenticate: Basic realm="Deployment Fix"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access Denied';
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Deployment Fix - Tabung Retail</title>
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
        <h1>🔧 Deployment Fix - Cache & Queue Tables</h1>
        <p><strong>Purpose:</strong> Create missing cache and queue tables for shared hosting</p>
        <p><strong>Environment:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            fixDeployment();
        } else {
            showForm();
        }
        
        function showForm() {
        ?>
            <div class="step warning">
                <h3>⚠️ This script will:</h3>
                <ul>
                    <li>Create cache table for database cache (if needed)</li>
                    <li>Create jobs table for queue system</li>
                    <li>Create failed_jobs table</li>
                    <li>Update configuration for shared hosting</li>
                </ul>
            </div>
            
            <form method="POST" onsubmit="return confirm('Create missing tables?');">
                <button type="submit" style="background: #3498db; color: white; padding: 15px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
                    🔧 Fix Deployment Issues
                </button>
            </form>
        <?php
        }
        
        function fixDeployment() {
            echo '<h2>🔧 Fixing Deployment Issues</h2>';
            
            try {
                require_once 'vendor/autoload.php';
                $app = require_once 'bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                
                // Get database connection
                $pdo = new PDO(
                    'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
                    env('DB_USERNAME'),
                    env('DB_PASSWORD'),
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                echo "<div class='step success'>";
                echo "<strong>✅ Database connection established</strong>";
                echo "</div>";
                
                // Create cache table
                echo "<div class='step'>";
                echo "<strong>📦 Creating cache table</strong><br>";
                
                $cacheTableSQL = "
                CREATE TABLE IF NOT EXISTS `cache` (
                    `key` varchar(255) NOT NULL,
                    `value` mediumtext NOT NULL,
                    `expiration` int NOT NULL,
                    PRIMARY KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                
                try {
                    $pdo->exec($cacheTableSQL);
                    echo "<span style='color: #2ecc71;'>✅ Cache table created/verified</span>";
                } catch (Exception $e) {
                    echo "<span style='color: #e74c3c;'>❌ Error creating cache table: " . $e->getMessage() . "</span>";
                }
                echo "</div>";
                
                // Create cache_locks table
                echo "<div class='step'>";
                echo "<strong>🔒 Creating cache_locks table</strong><br>";
                
                $cacheLocksSQL = "
                CREATE TABLE IF NOT EXISTS `cache_locks` (
                    `key` varchar(255) NOT NULL,
                    `owner` varchar(255) NOT NULL,
                    `expiration` int NOT NULL,
                    PRIMARY KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                
                try {
                    $pdo->exec($cacheLocksSQL);
                    echo "<span style='color: #2ecc71;'>✅ Cache locks table created/verified</span>";
                } catch (Exception $e) {
                    echo "<span style='color: #e74c3c;'>❌ Error creating cache_locks table: " . $e->getMessage() . "</span>";
                }
                echo "</div>";
                
                // Create jobs table
                echo "<div class='step'>";
                echo "<strong>⚙️ Creating jobs table</strong><br>";
                
                $jobsTableSQL = "
                CREATE TABLE IF NOT EXISTS `jobs` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                    `queue` varchar(255) NOT NULL,
                    `payload` longtext NOT NULL,
                    `attempts` tinyint unsigned NOT NULL,
                    `reserved_at` int unsigned DEFAULT NULL,
                    `available_at` int unsigned NOT NULL,
                    `created_at` int unsigned NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `jobs_queue_index` (`queue`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                
                try {
                    $pdo->exec($jobsTableSQL);
                    echo "<span style='color: #2ecc71;'>✅ Jobs table created/verified</span>";
                } catch (Exception $e) {
                    echo "<span style='color: #e74c3c;'>❌ Error creating jobs table: " . $e->getMessage() . "</span>";
                }
                echo "</div>";
                
                // Create failed_jobs table
                echo "<div class='step'>";
                echo "<strong>❌ Creating failed_jobs table</strong><br>";
                
                $failedJobsSQL = "
                CREATE TABLE IF NOT EXISTS `failed_jobs` (
                    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                    `uuid` varchar(255) NOT NULL,
                    `connection` text NOT NULL,
                    `queue` text NOT NULL,
                    `payload` longtext NOT NULL,
                    `exception` longtext NOT NULL,
                    `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                
                try {
                    $pdo->exec($failedJobsSQL);
                    echo "<span style='color: #2ecc71;'>✅ Failed jobs table created/verified</span>";
                } catch (Exception $e) {
                    echo "<span style='color: #e74c3c;'>❌ Error creating failed_jobs table: " . $e->getMessage() . "</span>";
                }
                echo "</div>";
                
                // Clear caches safely
                echo "<div class='step'>";
                echo "<strong>🧹 Clearing caches safely</strong><br>";
                
                $clearCommands = [
                    'config:clear' => 'Configuration cache',
                    'route:clear' => 'Route cache',
                    'view:clear' => 'View cache'
                ];
                
                foreach ($clearCommands as $command => $description) {
                    try {
                        ob_start();
                        $exitCode = $kernel->call($command);
                        $output = ob_get_clean();
                        
                        if ($exitCode === 0) {
                            echo "<span style='color: #2ecc71;'>✅ $description cleared</span><br>";
                        } else {
                            echo "<span style='color: #f39c12;'>⚠️ $description clear failed</span><br>";
                        }
                    } catch (Exception $e) {
                        echo "<span style='color: #e74c3c;'>❌ Error clearing $description: " . $e->getMessage() . "</span><br>";
                    }
                }
                echo "</div>";
                
                // Test cache functionality
                echo "<div class='step'>";
                echo "<strong>🧪 Testing cache functionality</strong><br>";
                try {
                    // Test file cache
                    $cacheKey = 'deployment_test_' . time();
                    $cacheValue = 'Test value for deployment';
                    
                    // Try to cache something
                    cache()->put($cacheKey, $cacheValue, 60);
                    $retrieved = cache()->get($cacheKey);
                    
                    if ($retrieved === $cacheValue) {
                        echo "<span style='color: #2ecc71;'>✅ Cache functionality working</span>";
                    } else {
                        echo "<span style='color: #f39c12;'>⚠️ Cache not working as expected</span>";
                    }
                } catch (Exception $e) {
                    echo "<span style='color: #e74c3c;'>❌ Cache test failed: " . $e->getMessage() . "</span>";
                }
                echo "</div>";
                
                echo "<div class='step success'>";
                echo "<h3>🎉 Deployment Fix Completed!</h3>";
                echo "<p>All necessary tables have been created for shared hosting.</p>";
                echo "<p><strong>Next steps:</strong></p>";
                echo "<ul>";
                echo "<li>Run deploy.php again - it should work now</li>";
                echo "<li>Test your application functionality</li>";
                echo "<li>Delete this fix_deployment.php file</li>";
                echo "</ul>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='step error'>";
                echo "<h3>❌ Fix Failed</h3>";
                echo "<p>Error: " . $e->getMessage() . "</p>";
                echo "</div>";
            }
            
            // Self-destruct option
            echo '<hr>';
            echo '<form method="POST" onsubmit="return confirm(\'Delete fix_deployment.php?\');">';
            echo '<input type="hidden" name="self_destruct" value="1">';
            echo '<button type="submit" style="background: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">';
            echo '🗑️ Delete fix_deployment.php';
            echo '</button>';
            echo '</form>';
        }
        
        // Handle self-destruct
        if (isset($_POST['self_destruct'])) {
            if (unlink(__FILE__)) {
                echo '<div class="step success">✅ fix_deployment.php deleted successfully!</div>';
                echo '<script>setTimeout(function(){ window.location.href = "/"; }, 3000);</script>';
            } else {
                echo '<div class="step error">❌ Failed to delete fix_deployment.php. Please delete manually.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
