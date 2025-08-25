<?php
// BASIC SERVER TEST - Upload ini sebagai info.php
echo "<h1>🔍 SERVER DIAGNOSIS</h1>";
echo "<hr>";

echo "<h2>📋 Basic Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Time: " . date('Y-m-d H:i:s') . "<br>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Path: " . __FILE__ . "<br>";

echo "<h2>🌐 Server Variables</h2>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "<br>";

echo "<h2>📁 File System Check</h2>";
$files = [
    'index.php',
    '.htaccess', 
    '.env',
    'vendor/autoload.php',
    'bootstrap/app.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file - EXISTS<br>";
    } else {
        echo "❌ $file - MISSING<br>";
    }
}

echo "<h2>📁 Directory Check</h2>";
$dirs = [
    'storage',
    'bootstrap',
    'app',
    'config',
    'vendor'
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ $dir/ - EXISTS<br>";
    } else {
        echo "❌ $dir/ - MISSING<br>";
    }
}

echo "<h2>🔐 Permission Check</h2>";
if (is_writable('.')) {
    echo "✅ Root directory is writable<br>";
} else {
    echo "❌ Root directory is NOT writable<br>";
}

if (is_dir('storage')) {
    if (is_writable('storage')) {
        echo "✅ Storage directory is writable<br>";
    } else {
        echo "❌ Storage directory is NOT writable<br>";
    }
}

echo "<h2>🗄️ Database Test</h2>";
if (file_exists('.env')) {
    echo "✅ .env file exists<br>";
    
    // Try to read database config
    $envContent = file_get_contents('.env');
    if (preg_match('/DB_HOST=(.+)/', $envContent, $match)) {
        echo "DB_HOST: " . trim($match[1]) . "<br>";
    }
    if (preg_match('/DB_DATABASE=(.+)/', $envContent, $match)) {
        echo "DB_DATABASE: " . trim($match[1]) . "<br>";
    }
    if (preg_match('/DB_USERNAME=(.+)/', $envContent, $match)) {
        echo "DB_USERNAME: " . trim($match[1]) . "<br>";
    }
} else {
    echo "❌ .env file missing<br>";
}

echo "<hr>";
echo "<h2>🚨 POSSIBLE ISSUES:</h2>";
echo "<ul>";
echo "<li>If you see this page: Basic PHP is working</li>";
echo "<li>If files missing: Upload incomplete or corrupted</li>";
echo "<li>If permissions wrong: Contact hosting support</li>";
echo "<li>If database config wrong: Update .env file</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Test Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Optional: Show PHPInfo if requested
if (isset($_GET['phpinfo'])) {
    echo "<hr><h2>📋 Full PHP Info</h2>";
    phpinfo();
}
?>
