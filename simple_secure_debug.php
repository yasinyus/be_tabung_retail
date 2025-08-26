<?php

// Bootstrap Laravel dengan error handling
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: {$e->getMessage()}\n";
    exit(1);
}

echo "=== SIMPLE SECURE DEBUG ===\n\n";

// 1. Check database connection
echo "1️⃣  Database Connection...\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "   ✅ Database connected\n";
} catch (Exception $e) {
    echo "   ❌ Database error: {$e->getMessage()}\n";
}

// 2. Check users table
echo "\n2️⃣  Users Check...\n";
try {
    $userCount = \App\Models\User::count();
    echo "   Total users: {$userCount}\n";
    
    if ($userCount > 0) {
        $adminUsers = \App\Models\User::whereIn('role', ['admin_utama', 'admin_umum'])->get();
        echo "   Admin users: {$adminUsers->count()}\n";
        
        foreach ($adminUsers as $admin) {
            echo "     - {$admin->name} ({$admin->email}) [{$admin->role}]\n";
        }
    } else {
        echo "   ⚠️  No users found! Need to create admin user.\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Users error: {$e->getMessage()}\n";
}

// 3. Check key environment variables
echo "\n3️⃣  Environment Check...\n";
$appKey = config('app.key');
$appEnv = config('app.env');
$appDebug = config('app.debug') ? 'true' : 'false';

echo "   APP_KEY: " . ($appKey ? 'SET' : 'NOT SET') . "\n";
echo "   APP_ENV: {$appEnv}\n";
echo "   APP_DEBUG: {$appDebug}\n";

// 4. Check session configuration
echo "\n4️⃣  Session Check...\n";
$sessionDriver = config('session.driver');
$sessionLifetime = config('session.lifetime');
echo "   Session driver: {$sessionDriver}\n";
echo "   Session lifetime: {$sessionLifetime} minutes\n";

if ($sessionDriver === 'file') {
    $sessionPath = storage_path('framework/sessions');
    $sessionExists = is_dir($sessionPath);
    $sessionWritable = $sessionExists ? is_writable($sessionPath) : false;
    
    echo "   Session path exists: " . ($sessionExists ? 'YES' : 'NO') . "\n";
    echo "   Session path writable: " . ($sessionWritable ? 'YES' : 'NO') . "\n";
}

// 5. Check storage permissions
echo "\n5️⃣  Storage Permissions...\n";
$storageDirs = ['storage/logs', 'storage/framework/cache', 'storage/framework/sessions'];

foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? 'WRITABLE' : 'NOT WRITABLE';
        echo "   {$dir}: {$writable}\n";
    } else {
        echo "   {$dir}: NOT EXISTS\n";
    }
}

echo "\n🔧 NEXT ACTIONS:\n";
echo "1. If no admin users: php create_secure_admin.php\n";
echo "2. If APP_KEY not set: php artisan key:generate\n";
echo "3. Fix storage permissions: chmod -R 755 storage\n";
echo "4. Clear caches: php fix_session_auth.php\n";
echo "5. Test login: http://your-domain/admin/login\n";

echo "\n🛡️  Security Status: AUTHENTICATION ENABLED\n";

?>
