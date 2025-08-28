<?php

echo "🔍 Server Bootstrap File Analysis\n\n";

// Check current bootstrap/app.php content
$bootstrapFile = __DIR__ . '/bootstrap/app.php';

if (!file_exists($bootstrapFile)) {
    echo "❌ bootstrap/app.php not found!\n";
    exit(1);
}

echo "📄 Current bootstrap/app.php content:\n";
echo str_repeat("=", 50) . "\n";

$content = file_get_contents($bootstrapFile);
echo $content;

echo "\n" . str_repeat("=", 50) . "\n";

// Check if our fix is there
if (strpos($content, 'AuthenticationException') !== false) {
    echo "✅ Route login fix FOUND in bootstrap/app.php\n";
} else {
    echo "❌ Route login fix NOT FOUND in bootstrap/app.php\n";
}

// Check current routes
echo "\n🛣️  Checking login routes...\n";
$output = shell_exec('php artisan route:list --name=login 2>&1');
echo $output;

echo "\n📋 File info:\n";
echo "File size: " . filesize($bootstrapFile) . " bytes\n";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($bootstrapFile)) . "\n";
echo "File permissions: " . substr(sprintf('%o', fileperms($bootstrapFile)), -4) . "\n";
