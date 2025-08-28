<?php

// Restore Backup Script
// Jalankan: php restore_backup.php

echo "🔄 PT GAS API - Restore Backup\n";
echo "==============================\n\n";

// Check if we're in the right directory
if (!file_exists('bootstrap/app.php')) {
    echo "❌ Error: bootstrap/app.php not found. Please run this script from the project root.\n";
    exit(1);
}

echo "✅ Project directory confirmed\n";

// Step 1: Find backup files
echo "\n📦 Looking for backup files...\n";
$backupFiles = [];
$filesToRestore = [
    'bootstrap/app.php',
    '.env',
    'routes/web.php',
    'routes/api.php'
];

foreach ($filesToRestore as $file) {
    $backupPattern = $file . '.backup.*';
    $backups = glob($backupPattern);
    
    if (!empty($backups)) {
        // Sort by modification time (newest first)
        usort($backups, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $latestBackup = $backups[0];
        $backupFiles[$file] = $latestBackup;
        echo "✅ Found backup: $latestBackup\n";
    } else {
        echo "⚠️ No backup found for: $file\n";
    }
}

if (empty($backupFiles)) {
    echo "❌ No backup files found! Cannot restore.\n";
    exit(1);
}

// Step 2: Confirm restore
echo "\n⚠️ WARNING: This will overwrite current files with backup versions!\n";
echo "Files to restore:\n";
foreach ($backupFiles as $original => $backup) {
    echo "  $original -> $backup\n";
}

echo "\nDo you want to continue? (y/N): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 'y' && trim(strtolower($line)) !== 'yes') {
    echo "❌ Restore cancelled.\n";
    exit(0);
}

// Step 3: Restore files
echo "\n🔄 Restoring files...\n";
foreach ($backupFiles as $original => $backup) {
    if (copy($backup, $original)) {
        echo "✅ Restored: $original\n";
    } else {
        echo "❌ Failed to restore: $original\n";
    }
}

// Step 4: Clear caches
echo "\n🧹 Clearing caches...\n";
$clearCommands = [
    'php artisan cache:clear',
    'php artisan config:clear',
    'php artisan route:clear',
    'php artisan view:clear',
    'php artisan optimize:clear'
];

foreach ($clearCommands as $command) {
    echo "Running: $command\n";
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ $command completed\n";
    } else {
        echo "⚠️ $command had issues (this might be normal)\n";
    }
}

// Step 5: Test basic functionality
echo "\n🧪 Testing basic functionality...\n";

// Test Laravel version
$output = [];
$returnCode = 0;
exec('php artisan --version 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ Laravel working: " . implode(' ', $output) . "\n";
} else {
    echo "❌ Laravel not working: " . implode(' ', $output) . "\n";
}

// Test route list
$output = [];
$returnCode = 0;
exec('php artisan route:list --path=api 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ Route list working\n";
    foreach ($output as $line) {
        if (strpos($line, 'terima-tabung') !== false) {
            echo "   Found: $line\n";
        }
    }
} else {
    echo "❌ Route list failed: " . implode(' ', $output) . "\n";
}

echo "\n🎉 Restore completed!\n";
echo "==============================\n";
echo "Next steps:\n";
echo "1. Test your application\n";
echo "2. Check if the original error is resolved\n";
echo "3. If needed, restart web server\n";
echo "\nTest commands:\n";
echo "php artisan route:list --path=api\n";
echo "curl -I http://your-domain.com/api/v1/test\n";
echo "\nIf you need to restart web server:\n";
echo "sudo systemctl restart nginx\n";
echo "sudo systemctl restart php8.2-fpm\n";
