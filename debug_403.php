<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING 403 FORBIDDEN ===\n\n";

try {
    // Check if users exist
    $userCount = \App\Models\User::count();
    echo "👥 Users in database: {$userCount}\n";
    
    if ($userCount > 0) {
        $users = \App\Models\User::select('id', 'name', 'email', 'role')->get();
        foreach ($users as $user) {
            echo "  - {$user->name} ({$user->email}) - Role: {$user->role}\n";
        }
    }
    
    // Check if role column exists
    $tableInfo = \Illuminate\Support\Facades\DB::select("DESCRIBE users");
    $hasRole = false;
    foreach ($tableInfo as $column) {
        if ($column->Field === 'role') {
            $hasRole = true;
            echo "✅ Role column exists: {$column->Type}\n";
            break;
        }
    }
    
    if (!$hasRole) {
        echo "❌ Role column missing!\n";
        echo "🔧 Run: php artisan migrate --path=database/migrations/2025_08_26_065741_add_role_to_users_table.php --force\n";
    }
    
    // Test admin user login
    if ($userCount > 0) {
        $admin = \App\Models\User::where('email', 'admin@ptgas.com')->first();
        if ($admin) {
            echo "✅ Admin user exists\n";
            echo "🔐 Can access panel: " . ($admin->canAccessPanel(null) ? 'YES' : 'NO') . "\n";
        } else {
            echo "❌ Admin user not found\n";
            echo "🔧 Run: php artisan db:seed --class=UserSeeder --force\n";
        }
    }
    
    // Check storage permissions
    $storagePerm = substr(sprintf('%o', fileperms(storage_path())), -4);
    echo "📁 Storage permissions: {$storagePerm}\n";
    
    // Check if Filament is properly installed
    echo "📦 Filament classes:\n";
    echo "  - Panel: " . (class_exists('\Filament\Panel') ? '✅' : '❌') . "\n";
    echo "  - Resource: " . (class_exists('\Filament\Resources\Resource') ? '✅' : '❌') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "📍 File: {$e->getFile()}:{$e->getLine()}\n";
}

echo "\n=== SOLUTIONS ===\n";
echo "1. 🚀 Quick fix (disable auth): php force_disable_auth.php\n";
echo "2. 🔧 Full setup: bash server_deployment_fix.sh\n";
echo "3. 📊 Check logs: tail -f storage/logs/laravel.log\n";

?>
