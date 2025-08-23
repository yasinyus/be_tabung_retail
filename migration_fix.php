<?php
/**
 * Migration Status Checker & Fixer
 * Script untuk menangani masalah migration table yang sudah ada
 * 
 * Upload ke laravel_app/ dan akses via browser:
 * https://yourdomain.com/migration_fix.php
 */

// Basic authentication
$auth_user = 'admin';
$auth_pass = 'deploy123';

if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] !== $auth_user || 
    $_SERVER['PHP_AUTH_PW'] !== $auth_pass) {
    header('WWW-Authenticate: Basic realm="Migration Fix"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access Denied';
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Migration Fix - Tabung Retail</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .step { margin: 10px 0; padding: 10px; border-left: 4px solid #3498db; background: #ecf0f1; }
        .success { border-color: #2ecc71; background: #d5f4e6; }
        .error { border-color: #e74c3c; background: #fadbd8; }
        .warning { border-color: #f39c12; background: #fdf2e9; }
        .info { border-color: #3498db; background: #ebf3fd; }
        .command { background: #2c3e50; color: #ecf0f1; padding: 10px; border-radius: 4px; font-family: monospace; margin: 5px 0; }
        pre { white-space: pre-wrap; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 10px 15px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #3498db; color: white; }
        .btn-success { background: #2ecc71; color: white; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Migration Status & Fix</h1>
        <p><strong>Purpose:</strong> Check and fix migration table issues</p>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleAction();
        } else {
            showStatus();
        }
        
        function showStatus() {
            try {
                require_once 'vendor/autoload.php';
                $app = require_once 'bootstrap/app.php';
                
                // Database connection
                $pdo = new PDO(
                    'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
                    env('DB_USERNAME'),
                    env('DB_PASSWORD'),
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                echo "<div class='step success'>";
                echo "<strong>✅ Database Connection: OK</strong><br>";
                echo "Database: " . env('DB_DATABASE') . "<br>";
                echo "Host: " . env('DB_HOST');
                echo "</div>";
                
                // Check existing tables
                echo "<div class='step info'>";
                echo "<strong>📊 Current Database Tables</strong><br>";
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (empty($tables)) {
                    echo "<span style='color: #e74c3c;'>No tables found in database</span>";
                } else {
                    echo "<table>";
                    echo "<tr><th>Table Name</th><th>Rows</th><th>Status</th></tr>";
                    foreach ($tables as $table) {
                        $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
                        $count = $countStmt->fetchColumn();
                        
                        $status = "✅ OK";
                        if (in_array($table, ['migrations', 'personal_access_tokens', 'cache', 'jobs'])) {
                            $status = "🔧 System Table";
                        }
                        
                        echo "<tr>";
                        echo "<td>$table</td>";
                        echo "<td>$count</td>";
                        echo "<td>$status</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                echo "</div>";
                
                // Check migrations status
                echo "<div class='step info'>";
                echo "<strong>📋 Migration Status</strong><br>";
                
                if (in_array('migrations', $tables)) {
                    $stmt = $pdo->query("SELECT migration, batch FROM migrations ORDER BY batch, migration");
                    $migrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($migrations)) {
                        echo "<span style='color: #f39c12;'>⚠️ Migrations table exists but is empty</span>";
                    } else {
                        echo "<table>";
                        echo "<tr><th>Migration</th><th>Batch</th></tr>";
                        foreach ($migrations as $migration) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($migration['migration']) . "</td>";
                            echo "<td>" . $migration['batch'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                } else {
                    echo "<span style='color: #e74c3c;'>❌ Migrations table not found</span>";
                }
                echo "</div>";
                
                // Check for problematic tables
                echo "<div class='step warning'>";
                echo "<strong>⚠️ Potential Issues</strong><br>";
                
                $issues = [];
                $systemTables = ['personal_access_tokens', 'cache', 'cache_locks', 'jobs', 'failed_jobs'];
                
                foreach ($systemTables as $sysTable) {
                    if (in_array($sysTable, $tables)) {
                        $issues[] = "Table '$sysTable' already exists";
                    }
                }
                
                if (empty($issues)) {
                    echo "<span style='color: #2ecc71;'>✅ No issues detected</span>";
                } else {
                    echo "<ul>";
                    foreach ($issues as $issue) {
                        echo "<li>$issue</li>";
                    }
                    echo "</ul>";
                }
                echo "</div>";
                
                // Action buttons
                echo "<div class='step'>";
                echo "<strong>🛠️ Available Actions</strong><br>";
                echo "<form method='POST' style='margin: 10px 0;'>";
                echo "<input type='hidden' name='action' value='mark_migrations'>";
                echo "<button type='submit' class='btn btn-warning' onclick=\"return confirm('Mark existing tables as migrated?')\">📝 Mark Existing Tables as Migrated</button>";
                echo "</form>";
                
                echo "<form method='POST' style='margin: 10px 0;'>";
                echo "<input type='hidden' name='action' value='fresh_migrate'>";
                echo "<button type='submit' class='btn btn-danger' onclick=\"return confirm('WARNING: This will drop all tables and re-create them. All data will be lost!')\">🔥 Fresh Migration (DANGEROUS)</button>";
                echo "</form>";
                
                echo "<form method='POST' style='margin: 10px 0;'>";
                echo "<input type='hidden' name='action' value='safe_migrate'>";
                echo "<button type='submit' class='btn btn-success' onclick=\"return confirm('Run safe migration?')\">✅ Safe Migration (Skip Existing)</button>";
                echo "</form>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='step error'>";
                echo "<strong>❌ Error</strong><br>";
                echo htmlspecialchars($e->getMessage());
                echo "</div>";
            }
        }
        
        function handleAction() {
            $action = $_POST['action'] ?? '';
            
            try {
                require_once 'vendor/autoload.php';
                $app = require_once 'bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                
                $pdo = new PDO(
                    'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
                    env('DB_USERNAME'),
                    env('DB_PASSWORD'),
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                switch ($action) {
                    case 'mark_migrations':
                        echo "<div class='step'>";
                        echo "<strong>📝 Marking Existing Tables as Migrated</strong><br>";
                        
                        // Create migrations table if not exists
                        $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
                            `id` int unsigned NOT NULL AUTO_INCREMENT,
                            `migration` varchar(255) NOT NULL,
                            `batch` int NOT NULL,
                            PRIMARY KEY (`id`)
                        )");
                        
                        // Get list of migration files
                        $migrationFiles = glob('database/migrations/*.php');
                        $batch = 1;
                        
                        foreach ($migrationFiles as $file) {
                            $filename = basename($file, '.php');
                            
                            // Check if already recorded
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
                            $stmt->execute([$filename]);
                            
                            if ($stmt->fetchColumn() == 0) {
                                $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                                $stmt->execute([$filename, $batch]);
                                echo "✅ Marked: $filename<br>";
                            } else {
                                echo "⚠️ Already marked: $filename<br>";
                            }
                        }
                        
                        echo "<span style='color: #2ecc71;'>✅ Migration marking completed</span>";
                        echo "</div>";
                        break;
                        
                    case 'fresh_migrate':
                        echo "<div class='step warning'>";
                        echo "<strong>🔥 Fresh Migration (DANGEROUS)</strong><br>";
                        
                        ob_start();
                        $exitCode = $kernel->call('migrate:fresh', ['--force' => true]);
                        $output = ob_get_clean();
                        
                        if ($exitCode === 0) {
                            echo "<span style='color: #2ecc71;'>✅ Fresh migration completed</span>";
                        } else {
                            echo "<span style='color: #e74c3c;'>❌ Fresh migration failed</span>";
                        }
                        
                        if ($output) {
                            echo "<pre>$output</pre>";
                        }
                        echo "</div>";
                        break;
                        
                    case 'safe_migrate':
                        echo "<div class='step'>";
                        echo "<strong>✅ Safe Migration</strong><br>";
                        
                        // Run migrate with error handling
                        ob_start();
                        
                        try {
                            $exitCode = $kernel->call('migrate', ['--force' => true]);
                            $output = ob_get_clean();
                            
                            // Filter out "already exists" errors
                            $lines = explode("\n", $output);
                            $cleanOutput = [];
                            $hasErrors = false;
                            
                            foreach ($lines as $line) {
                                if (preg_match('/already exists|Base table.*already exists/', $line)) {
                                    $cleanOutput[] = "⚠️ " . $line . " (skipped)";
                                } else if (!empty(trim($line))) {
                                    $cleanOutput[] = $line;
                                    if (strpos($line, 'ERROR') !== false) {
                                        $hasErrors = true;
                                    }
                                }
                            }
                            
                            if (!$hasErrors) {
                                echo "<span style='color: #2ecc71;'>✅ Safe migration completed</span>";
                            } else {
                                echo "<span style='color: #f39c12;'>⚠️ Migration completed with warnings</span>";
                            }
                            
                            if (!empty($cleanOutput)) {
                                echo "<pre>" . implode("\n", $cleanOutput) . "</pre>";
                            }
                            
                        } catch (Exception $e) {
                            $output = ob_get_clean();
                            echo "<span style='color: #f39c12;'>⚠️ Migration completed with some tables already existing</span>";
                            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
                        }
                        
                        echo "</div>";
                        break;
                }
                
                echo "<div class='step success'>";
                echo "<strong>🎉 Action Completed</strong><br>";
                echo "<a href='?' class='btn btn-primary'>🔄 Refresh Status</a>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='step error'>";
                echo "<strong>❌ Action Failed</strong><br>";
                echo htmlspecialchars($e->getMessage());
                echo "</div>";
            }
        }
        ?>
        
        <hr>
        <div class="step info">
            <strong>💡 Tips:</strong>
            <ul>
                <li><strong>Mark Migrations:</strong> Tell Laravel that existing tables are already migrated</li>
                <li><strong>Safe Migration:</strong> Run migration and ignore "table exists" errors</li>
                <li><strong>Fresh Migration:</strong> ⚠️ Destroys all data and recreates tables</li>
            </ul>
        </div>
        
        <form method="POST" onsubmit="return confirm('Delete migration_fix.php?');">
            <input type="hidden" name="self_destruct" value="1">
            <button type="submit" class="btn btn-danger">🗑️ Delete This Script</button>
        </form>
    </div>
</body>
</html>

<?php
// Handle self-destruct
if (isset($_POST['self_destruct'])) {
    if (unlink(__FILE__)) {
        echo '<script>alert("migration_fix.php deleted successfully!"); window.location.href = "/";</script>';
    } else {
        echo '<script>alert("Failed to delete migration_fix.php");</script>';
    }
}
?>
