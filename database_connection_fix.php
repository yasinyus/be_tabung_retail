<?php
/**
 * Database Connection Diagnostic & Fix Tool
 * Untuk mengatasi error: Access denied for user (using password: NO)
 */

// Simple authentication
session_start();
if (!isset($_SESSION['authenticated'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_POST['username'] === 'admin' && $_POST['password'] === 'deploy123') {
            $_SESSION['authenticated'] = true;
        } else {
            $error = "Invalid credentials!";
        }
    }
    
    if (!isset($_SESSION['authenticated'])) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Database Connection Fix</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
                .error { color: red; background: #ffebee; padding: 10px; border-radius: 5px; margin: 10px 0; }
                .success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
                .info { color: blue; background: #e3f2fd; padding: 10px; border-radius: 5px; margin: 10px 0; }
                .warning { color: orange; background: #fff3e0; padding: 10px; border-radius: 5px; margin: 10px 0; }
                form { background: #f5f5f5; padding: 20px; border-radius: 5px; }
                input, button { padding: 10px; margin: 5px; width: 200px; }
                button { background: #007cba; color: white; border: none; cursor: pointer; }
                button:hover { background: #005a8b; }
                pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
                .code { font-family: monospace; background: #f0f0f0; padding: 2px 4px; }
            </style>
        </head>
        <body>
            <h1>🔧 Database Connection Fix Tool</h1>
            <div class="error">
                <strong>Error Detected:</strong><br>
                SQLSTATE[28000] [1045] Access denied for user 'gass1498'@'localhost' (using password: NO)
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <h3>Login to Fix Database Connection</h3>
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Login & Fix Database</button>
            </form>
            
            <div class="info">
                <strong>Default credentials:</strong> admin / deploy123
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Main diagnostic and fix logic
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #e3f2fd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: orange; background: #fff3e0; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .step { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba; }
        pre { background: #f0f0f0; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .code { font-family: monospace; background: #f0f0f0; padding: 2px 4px; }
        button { background: #007cba; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; margin: 5px; }
        button:hover { background: #005a8b; }
        .action-button { background: #4caf50; }
        .action-button:hover { background: #45a049; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔧 Database Connection Diagnostic & Fix</h1>
    
    <div class="error">
        <strong>⚠️ Error Detected:</strong><br>
        <code>SQLSTATE[28000] [1045] Access denied for user 'gass1498'@'localhost' (using password: NO)</code>
    </div>

    <?php
    
    // Step 1: Check .env file
    echo '<div class="step">';
    echo '<h3>🔍 Step 1: Checking .env Configuration</h3>';
    
    $envPath = __DIR__ . '/.env';
    if (!file_exists($envPath)) {
        echo '<div class="error">❌ .env file not found!</div>';
        echo '<div class="info">Please ensure .env file exists in the Laravel root directory.</div>';
    } else {
        echo '<div class="success">✅ .env file found</div>';
        
        $envContent = file_get_contents($envPath);
        $dbConfig = [];
        
        // Parse database configuration
        preg_match('/DB_CONNECTION=(.+)/', $envContent, $matches);
        $dbConfig['connection'] = isset($matches[1]) ? trim($matches[1]) : 'not set';
        
        preg_match('/DB_HOST=(.+)/', $envContent, $matches);
        $dbConfig['host'] = isset($matches[1]) ? trim($matches[1]) : 'not set';
        
        preg_match('/DB_PORT=(.+)/', $envContent, $matches);
        $dbConfig['port'] = isset($matches[1]) ? trim($matches[1]) : 'not set';
        
        preg_match('/DB_DATABASE=(.+)/', $envContent, $matches);
        $dbConfig['database'] = isset($matches[1]) ? trim($matches[1]) : 'not set';
        
        preg_match('/DB_USERNAME=(.+)/', $envContent, $matches);
        $dbConfig['username'] = isset($matches[1]) ? trim($matches[1]) : 'not set';
        
        preg_match('/DB_PASSWORD=(.+)/', $envContent, $matches);
        $dbConfig['password'] = isset($matches[1]) ? trim($matches[1]) : 'not set';
        
        echo '<table>';
        echo '<tr><th>Configuration</th><th>Current Value</th><th>Status</th></tr>';
        
        foreach ($dbConfig as $key => $value) {
            $status = $value !== 'not set' && $value !== '' ? '✅ Set' : '❌ Missing';
            $statusClass = $value !== 'not set' && $value !== '' ? 'success' : 'error';
            echo "<tr><td>DB_" . strtoupper($key) . "</td><td><code>" . htmlspecialchars($value) . "</code></td><td><span class='$statusClass'>$status</span></td></tr>";
        }
        echo '</table>';
        
        // Check for common issues
        if ($dbConfig['password'] === 'not set' || $dbConfig['password'] === '') {
            echo '<div class="error">❌ <strong>ISSUE FOUND:</strong> DB_PASSWORD is empty or missing!</div>';
        }
        
        if ($dbConfig['username'] === 'not set' || $dbConfig['username'] === '') {
            echo '<div class="error">❌ <strong>ISSUE FOUND:</strong> DB_USERNAME is empty or missing!</div>';
        }
        
        if ($dbConfig['database'] === 'not set' || $dbConfig['database'] === '') {
            echo '<div class="error">❌ <strong>ISSUE FOUND:</strong> DB_DATABASE is empty or missing!</div>';
        }
    }
    echo '</div>';
    
    // Step 2: Test Connection
    echo '<div class="step">';
    echo '<h3>🔍 Step 2: Testing Database Connection</h3>';
    
    if (isset($_POST['test_connection'])) {
        $host = $_POST['test_host'] ?? 'localhost';
        $username = $_POST['test_username'] ?? '';
        $password = $_POST['test_password'] ?? '';
        $database = $_POST['test_database'] ?? '';
        
        try {
            $dsn = "mysql:host=$host;dbname=$database";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="success">✅ Database connection successful!</div>';
            
            // Test query
            $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as current_db");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo '<div class="info">';
            echo '<strong>Connection Details:</strong><br>';
            echo 'MySQL Version: ' . $result['version'] . '<br>';
            echo 'Current Database: ' . $result['current_db'] . '<br>';
            echo '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="error">❌ Connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    
    ?>
    
    <form method="POST" style="background: #f9f9f9; padding: 20px; border-radius: 5px;">
        <h4>Test Database Connection:</h4>
        <table>
            <tr>
                <td>Host:</td>
                <td><input type="text" name="test_host" value="localhost" style="width: 200px;"></td>
            </tr>
            <tr>
                <td>Username:</td>
                <td><input type="text" name="test_username" value="<?= htmlspecialchars($dbConfig['username'] ?? '') ?>" style="width: 200px;"></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="test_password" placeholder="Enter password" style="width: 200px;"></td>
            </tr>
            <tr>
                <td>Database:</td>
                <td><input type="text" name="test_database" value="<?= htmlspecialchars($dbConfig['database'] ?? '') ?>" style="width: 200px;"></td>
            </tr>
        </table>
        <button type="submit" name="test_connection" class="action-button">Test Connection</button>
    </form>
    
    <?php echo '</div>'; ?>
    
    <!-- Step 3: Fix Options -->
    <div class="step">
        <h3>🔧 Step 3: Fix Options</h3>
        
        <?php if (isset($_POST['update_env'])): ?>
            <div class="info">Updating .env file...</div>
            
            $newEnvContent = $envContent;
            
            // Update database credentials
            if (!empty($_POST['fix_username'])) {
                $newEnvContent = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . $_POST['fix_username'], $newEnvContent);
            }
            
            if (!empty($_POST['fix_password'])) {
                $newEnvContent = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . $_POST['fix_password'], $newEnvContent);
            }
            
            if (!empty($_POST['fix_database'])) {
                $newEnvContent = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . $_POST['fix_database'], $newEnvContent);
            }
            
            if (!empty($_POST['fix_host'])) {
                $newEnvContent = preg_replace('/DB_HOST=.*/', 'DB_HOST=' . $_POST['fix_host'], $newEnvContent);
            }
            
            // Write updated .env
            if (file_put_contents($envPath, $newEnvContent)) {
                echo '<div class="success">✅ .env file updated successfully!</div>';
                echo '<div class="info">Please test the connection again to verify the fix.</div>';
            } else {
                echo '<div class="error">❌ Failed to update .env file. Check file permissions.</div>';
            }
        <?php endif; ?>
        
        <form method="POST" style="background: #f9f9f9; padding: 20px; border-radius: 5px;">
            <h4>Update Database Credentials:</h4>
            <table>
                <tr>
                    <td>Host:</td>
                    <td><input type="text" name="fix_host" value="localhost" style="width: 200px;"></td>
                </tr>
                <tr>
                    <td>Username:</td>
                    <td><input type="text" name="fix_username" placeholder="e.g. gass1498_user" style="width: 200px;"></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="fix_password" placeholder="Database password" style="width: 200px;"></td>
                </tr>
                <tr>
                    <td>Database:</td>
                    <td><input type="text" name="fix_database" placeholder="e.g. gass1498_tabung" style="width: 200px;"></td>
                </tr>
            </table>
            <button type="submit" name="update_env" class="action-button">Update .env File</button>
        </form>
    </div>
    
    <!-- Step 4: Common Solutions -->
    <div class="step">
        <h3>💡 Step 4: Common Solutions for Shared Hosting</h3>
        
        <div class="warning">
            <strong>Common Issues & Solutions:</strong>
        </div>
        
        <h4>1. Database Credentials Format in Shared Hosting:</h4>
        <pre>
# ❌ Wrong format:
DB_USERNAME=gass1498
DB_PASSWORD=
DB_DATABASE=tabung_retail

# ✅ Correct format for shared hosting:
DB_USERNAME=gass1498_user        # Usually: cpanel_username + underscore + db_user
DB_PASSWORD=your_actual_password  # The password you set when creating DB user
DB_DATABASE=gass1498_tabung      # Usually: cpanel_username + underscore + db_name
</pre>
        
        <h4>2. Check cPanel Database Settings:</h4>
        <ul>
            <li>Go to cPanel → MySQL Databases</li>
            <li>Check your database name (usually prefixed with your username)</li>
            <li>Check your database user (usually prefixed with your username)</li>
            <li>Make sure the user has ALL PRIVILEGES on the database</li>
            <li>If password is forgotten, change it in cPanel</li>
        </ul>
        
        <h4>3. File Permissions Check:</h4>
        <pre>
# .env file should be readable by PHP
chmod 644 .env

# Laravel app folder permissions
chmod -R 755 laravel_app/
chmod -R 775 laravel_app/storage/
chmod -R 775 laravel_app/bootstrap/cache/
</pre>
        
        <h4>4. Laravel Config Cache Clear:</h4>
        <div class="info">
            After updating .env, run the deployment script again or clear config cache manually.
        </div>
    </div>
    
    <!-- Step 5: Quick Actions -->
    <div class="step">
        <h3>⚡ Step 5: Quick Actions</h3>
        
        <button onclick="window.location.href='deploy.php'" style="background: #ff9800;">
            🚀 Run Deployment Script
        </button>
        
        <button onclick="window.location.href='fix_deployment.php'" style="background: #9c27b0;">
            🔧 Run General Fix Script
        </button>
        
        <button onclick="location.reload();" style="background: #607d8b;">
            🔄 Refresh Diagnostic
        </button>
    </div>
    
    <div class="info">
        <strong>📝 Next Steps:</strong><br>
        1. Update database credentials using the form above<br>
        2. Test the connection to verify it works<br>
        3. Run the deployment script to complete setup<br>
        4. Delete this diagnostic script for security
    </div>
    
    <form method="POST" style="margin-top: 20px;">
        <button type="submit" name="logout" style="background: #f44336;">🚪 Logout</button>
    </form>
    
    <?php
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    ?>
    
</body>
</html>
