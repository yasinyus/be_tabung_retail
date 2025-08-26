<?php

// TEMPORARY AUTH BYPASS FOR SERVER DEBUGGING
// Run this if you still get 403 after running server_deployment_fix.sh

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEMPORARY AUTH BYPASS FOR SERVER ===\n";

// Create AdminPanelProvider backup
$providerPath = 'app/Providers/Filament/AdminPanelProvider.php';
$backupPath = 'app/Providers/Filament/AdminPanelProvider.php.backup';

if (!file_exists($backupPath)) {
    copy($providerPath, $backupPath);
    echo "✅ Created backup of AdminPanelProvider\n";
}

// Temporarily disable auth for testing
$content = file_get_contents($providerPath);
$content = str_replace('->login()', '// ->login()', $content);
$content = str_replace('->authGuard(\'web\')', '// ->authGuard(\'web\')', $content);
$content = str_replace('Authenticate::class,', '// Authenticate::class,', $content);

file_put_contents($providerPath, $content);

echo "✅ Temporarily disabled authentication\n";
echo "🌐 You can now access admin panel without login\n";
echo "⚠️  REMEMBER TO RESTORE AUTH AFTER TESTING!\n\n";

echo "To restore authentication later, run:\n";
echo "cp app/Providers/Filament/AdminPanelProvider.php.backup app/Providers/Filament/AdminPanelProvider.php\n";
echo "php artisan config:clear\n";

?>
