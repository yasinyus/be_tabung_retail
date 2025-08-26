<?php

echo "=== RESTORE ALL BACKUPS ===\n";

$backups = [
    'app/Models/User.php.backup' => 'app/Models/User.php',
    'app/Providers/Filament/AdminPanelProvider.php.backup' => 'app/Providers/Filament/AdminPanelProvider.php',
    'app/Providers/Filament/AdminPanelProvider.php.backup2' => 'app/Providers/Filament/AdminPanelProvider.php',
    'app/Providers/Filament/AdminPanelProvider.php.backup3' => 'app/Providers/Filament/AdminPanelProvider.php',
];

foreach ($backups as $backup => $original) {
    if (file_exists($backup)) {
        copy($backup, $original);
        unlink($backup);
        echo "✅ Restored {$original}\n";
    }
}

// Clear cache
system('php artisan config:clear');
echo "✅ Cache cleared\n";
echo "🔄 All files restored to original state\n";

?>
