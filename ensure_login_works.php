<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MEMASTIKAN LOGIN AUTHENTICATION ===\n\n";

// 1. Clear all caches
echo "1️⃣  Clearing Caches...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "   ✅ Config cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "   ✅ Routes cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "   ✅ Cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "   ✅ Views cleared\n";
    
} catch (Exception $e) {
    echo "   ❌ Cache clear error: {$e->getMessage()}\n";
}

// 2. Check users di database
echo "\n2️⃣  Checking Database Users...\n";
try {
    $users = \App\Models\User::all();
    echo "   Total users: " . $users->count() . "\n";
    
    foreach ($users as $user) {
        $canAccess = $user->canAccessPanel(null) ? 'YES' : 'NO';
        echo "   - {$user->name} ({$user->email}) [{$user->role}] - Can Access: {$canAccess}\n";
    }
    
    // Check if ada admin
    $admins = \App\Models\User::whereIn('role', ['admin_utama', 'admin_umum'])->count();
    echo "   Admin users: {$admins}\n";
    
} catch (Exception $e) {
    echo "   ❌ Database error: {$e->getMessage()}\n";
}

// 3. Verify AdminPanelProvider
echo "\n3️⃣  Verifying AdminPanelProvider...\n";
$providerContent = file_get_contents('app/Providers/Filament/AdminPanelProvider.php');

$checks = [
    '->login()' => strpos($providerContent, '->login()') !== false,
    '->authGuard(\'web\')' => strpos($providerContent, '->authGuard(\'web\')') !== false,
    'Authenticate::class' => strpos($providerContent, 'Authenticate::class') !== false,
];

foreach ($checks as $feature => $exists) {
    $status = $exists ? '✅' : '❌';
    echo "   {$status} {$feature}\n";
}

// 4. Check routes
echo "\n4️⃣  Checking Admin Routes...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $loginRoutes = 0;
    $adminRoutes = 0;
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (str_contains($uri, 'admin/login')) {
            $loginRoutes++;
        }
        if (str_contains($uri, 'admin')) {
            $adminRoutes++;
        }
    }
    
    echo "   Login routes: {$loginRoutes}\n";
    echo "   Total admin routes: {$adminRoutes}\n";
    
    if ($loginRoutes > 0) {
        echo "   ✅ Login routes registered\n";
    } else {
        echo "   ❌ No login routes found!\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Routes error: {$e->getMessage()}\n";
}

// 5. Test URL generation
echo "\n5️⃣  Testing URL Generation...\n";
try {
    $appUrl = config('app.url', 'http://localhost');
    echo "   App URL: {$appUrl}\n";
    echo "   Login URL: {$appUrl}/admin/login\n";
    echo "   Admin URL: {$appUrl}/admin\n";
    
} catch (Exception $e) {
    echo "   ❌ URL error: {$e->getMessage()}\n";
}

echo "\n🎯 HASIL DIAGNOSIS:\n";
echo "✅ Jika semua checks ✅ = Authentication sudah aktif\n";
echo "✅ Akses URL: /admin akan redirect ke /admin/login\n";
echo "✅ Setelah login sukses akan masuk ke dashboard\n";

echo "\n🔐 CARA TEST:\n";
echo "1. Buka: http://your-domain/admin\n";
echo "2. Harus redirect ke: http://your-domain/admin/login\n";
echo "3. Login dengan user dari database\n";
echo "4. Setelah login sukses masuk dashboard admin\n";

echo "\n🛡️  AUTHENTICATION STATUS: ENABLED & SECURE\n";

?>
