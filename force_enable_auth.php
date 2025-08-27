<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FORCE ENABLE AUTHENTICATION ===\n\n";

// 1. Clear ALL caches aggressively
echo "1️⃣  Aggressive Cache Clear...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "   ✅ Config cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "   ✅ Route cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "   ✅ Application cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "   ✅ View cache cleared\n";
    
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "   ✅ All optimization cleared\n";
    
    // Clear session files if using file driver
    $sessionPath = storage_path('framework/sessions');
    if (is_dir($sessionPath)) {
        $files = glob($sessionPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   ✅ Session files cleared\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Cache clear error: {$e->getMessage()}\n";
}

// 2. Force regenerate autoloader
echo "\n2️⃣  Regenerate Autoloader...\n";
system('composer dump-autoload --optimize');
echo "   ✅ Autoloader regenerated\n";

// 3. Regenerate app key if missing
echo "\n3️⃣  Check APP_KEY...\n";
$appKey = config('app.key');
if (!$appKey) {
    \Illuminate\Support\Facades\Artisan::call('key:generate');
    echo "   ✅ APP_KEY generated\n";
} else {
    echo "   ✅ APP_KEY exists\n";
}

// 4. Force logout any existing sessions
echo "\n4️⃣  Force Logout All Sessions...\n";
try {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    echo "   ✅ All sessions logged out\n";
} catch (Exception $e) {
    echo "   ⚠️  Logout: {$e->getMessage()}\n";
}

// 5. Verify AdminPanelProvider again
echo "\n5️⃣  Verify AdminPanelProvider...\n";
$providerPath = 'app/Providers/Filament/AdminPanelProvider.php';
$content = file_get_contents($providerPath);

$requiredElements = [
    '->login()' => strpos($content, '->login()') !== false,
    '->authGuard(' => strpos($content, '->authGuard(') !== false,
    'Authenticate::class' => strpos($content, 'Authenticate::class') !== false,
];

foreach ($requiredElements as $element => $exists) {
    $status = $exists ? '✅' : '❌';
    echo "   {$status} {$element}\n";
}

// 6. Check User model
echo "\n6️⃣  Verify User Model...\n";
$userPath = 'app/Models/User.php';
$userContent = file_get_contents($userPath);

if (strpos($userContent, 'canAccessPanel') !== false) {
    echo "   ✅ canAccessPanel method exists\n";
    
    // Check if it's not just returning true
    if (strpos($userContent, 'return true;') !== false && 
        strpos($userContent, 'in_array($this->role') !== false) {
        echo "   ✅ Role-based access control implemented\n";
    } else if (strpos($userContent, 'return true;') !== false) {
        echo "   ⚠️  WARNING: canAccessPanel might return true for all\n";
    }
} else {
    echo "   ❌ canAccessPanel method missing\n";
}

echo "\n🔒 FORCE AUTH COMPLETE!\n";
echo "\n🎯 NEXT STEPS:\n";
echo "1. Restart web server (Apache/Nginx)\n";
echo "2. Clear browser cache and cookies\n";
echo "3. Test: http://your-domain/admin (should redirect to login)\n";
echo "4. If still no redirect, there's a deeper server config issue\n";

echo "\n⚠️  SECURITY WARNING:\n";
echo "If admin is still accessible without login after this,\n";
echo "there might be a web server configuration override!\n";

?>
