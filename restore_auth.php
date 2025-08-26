<?php

// RESTORE AUTHENTICATION AFTER TESTING

echo "=== RESTORING AUTHENTICATION ===\n";

$providerPath = 'app/Providers/Filament/AdminPanelProvider.php';
$backupPath = 'app/Providers/Filament/AdminPanelProvider.php.backup';

if (file_exists($backupPath)) {
    copy($backupPath, $providerPath);
    unlink($backupPath);
    echo "✅ Authentication restored from backup\n";
} else {
    echo "❌ No backup found\n";
}

// Clear cache
system('php artisan config:clear');
echo "✅ Cache cleared\n";
echo "🔐 Authentication is now enabled\n";
echo "📧 Login with: admin@ptgas.com / password123\n";

?>
