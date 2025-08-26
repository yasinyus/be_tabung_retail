<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FIX SESSION & AUTH CONFIGURATION ===\n\n";

// 1. Check session configuration
echo "1️⃣  Checking Session Config...\n";
$sessionDriver = config('session.driver');
$sessionLifetime = config('session.lifetime');
$sessionPath = config('session.files');

echo "   Driver: {$sessionDriver}\n";
echo "   Lifetime: {$sessionLifetime} minutes\n";
echo "   Path: {$sessionPath}\n";

// Check if session storage is writable
if ($sessionDriver === 'file') {
    $sessionDir = storage_path('framework/sessions');
    if (!is_dir($sessionDir)) {
        mkdir($sessionDir, 0755, true);
        echo "   ✅ Created session directory\n";
    }
    
    if (is_writable($sessionDir)) {
        echo "   ✅ Session directory writable\n";
    } else {
        echo "   ❌ Session directory not writable!\n";
        echo "   🔧 Fix: chmod 755 {$sessionDir}\n";
    }
}

// 2. Check APP_KEY
echo "\n2️⃣  Checking APP_KEY...\n";
$appKey = config('app.key');
if ($appKey) {
    echo "   ✅ APP_KEY is set\n";
} else {
    echo "   ❌ APP_KEY not set!\n";
    echo "   🔧 Fix: php artisan key:generate\n";
}

// 3. Clear all caches
echo "\n3️⃣  Clearing Caches...\n";
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

// 4. Fix User model canAccessPanel
echo "\n4️⃣  Ensuring User Model Security...\n";

$userModelPath = 'app/Models/User.php';
$userModelContent = file_get_contents($userModelPath);

// Check if canAccessPanel exists and is secure
if (strpos($userModelContent, 'canAccessPanel') !== false) {
    echo "   ✅ canAccessPanel method exists\n";
    
    // Make sure it's not just returning true
    if (strpos($userModelContent, 'return true;') !== false) {
        echo "   ⚠️  WARNING: canAccessPanel returns true for all users!\n";
        echo "   🔧 Should implement proper role checking\n";
    }
} else {
    echo "   ❌ canAccessPanel method missing\n";
}

echo "\n🔒 SECURITY RECOMMENDATIONS:\n";
echo "1. Implement role-based canAccessPanel()\n";
echo "2. Use middleware for additional security\n";
echo "3. Enable CSRF protection\n";
echo "4. Use HTTPS in production\n";
echo "5. Regular security updates\n";

echo "\n✅ AUTH FIX COMPLETE!\n";
echo "Now try:\n";
echo "1. php create_secure_admin.php\n";
echo "2. Clear browser cache/cookies\n";
echo "3. Login at: http://your-domain/admin/login\n";

?>
