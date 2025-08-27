<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUG 403 AFTER LOGIN ===\n\n";

// 1. Check current auth state
echo "1️⃣  Current Authentication State...\n";
try {
    $isAuthenticated = \Illuminate\Support\Facades\Auth::guard('web')->check();
    echo "   Authenticated: " . ($isAuthenticated ? 'YES' : 'NO') . "\n";
    
    if ($isAuthenticated) {
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        echo "   Current user: {$user->name}\n";
        echo "   Email: {$user->email}\n";
        echo "   Role: {$user->role}\n";
        
        // Test canAccessPanel
        $canAccess = $user->canAccessPanel(null);
        echo "   Can access panel: " . ($canAccess ? 'YES' : 'NO') . "\n";
        
        if (!$canAccess) {
            echo "   ❌ PROBLEM: User cannot access panel!\n";
        }
        
    } else {
        echo "   ⚠️  No authenticated user found\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Auth check error: {$e->getMessage()}\n";
}

// 2. Check all users and their panel access
echo "\n2️⃣  All Users Panel Access Check...\n";
try {
    $users = \App\Models\User::all();
    echo "   Total users: " . $users->count() . "\n\n";
    
    foreach ($users as $user) {
        $canAccess = $user->canAccessPanel(null);
        $status = $canAccess ? '✅' : '❌';
        echo "   {$status} {$user->name} ({$user->email}) [{$user->role}]\n";
        
        if (!$canAccess) {
            echo "       → Cannot access panel with role: {$user->role}\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Users check error: {$e->getMessage()}\n";
}

// 3. Check UserResource authorization methods
echo "\n3️⃣  UserResource Authorization Methods...\n";
try {
    $resourceClass = \App\Filament\Resources\UserResource::class;
    
    if (class_exists($resourceClass)) {
        echo "   ✅ UserResource class exists\n";
        
        // Check authorization methods
        $methods = [
            'canViewAny' => 'canViewAny',
            'canCreate' => 'canCreate', 
            'canView' => 'canView',
            'canEdit' => 'canEdit',
            'canDelete' => 'canDelete',
            'canDeleteAny' => 'canDeleteAny'
        ];
        
        foreach ($methods as $description => $method) {
            if (method_exists($resourceClass, $method)) {
                echo "   ✅ {$description} method exists\n";
                
                try {
                    if (in_array($method, ['canViewAny', 'canCreate', 'canDeleteAny'])) {
                        $result = $resourceClass::$method();
                    } else {
                        // For methods that need record, use null as test
                        $result = $resourceClass::$method(null);
                    }
                    echo "       → Returns: " . ($result ? 'TRUE' : 'FALSE') . "\n";
                } catch (Exception $e) {
                    echo "       → Error: {$e->getMessage()}\n";
                }
            } else {
                echo "   ❌ {$description} method missing\n";
            }
        }
        
    } else {
        echo "   ❌ UserResource class not found!\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Resource check error: {$e->getMessage()}\n";
}

// 4. Check User model canAccessPanel method
echo "\n4️⃣  User Model canAccessPanel Implementation...\n";
$userModelPath = 'app/Models/User.php';
if (file_exists($userModelPath)) {
    $content = file_get_contents($userModelPath);
    
    if (strpos($content, 'canAccessPanel') !== false) {
        echo "   ✅ canAccessPanel method exists\n";
        
        // Extract the method content
        preg_match('/public function canAccessPanel.*?\{(.*?)\}/s', $content, $matches);
        if (isset($matches[1])) {
            $methodContent = trim($matches[1]);
            echo "   Method content preview:\n";
            echo "   " . str_replace("\n", "\n   ", $methodContent) . "\n";
        }
    } else {
        echo "   ❌ canAccessPanel method not found!\n";
    }
} else {
    echo "   ❌ User model file not found!\n";
}

echo "\n🔧 COMMON FIXES FOR 403 AFTER LOGIN:\n";
echo "1. Add canViewAny() method to UserResource returning true\n";
echo "2. Check user role is in allowed roles list\n";
echo "3. Ensure canAccessPanel returns true for user's role\n";
echo "4. Clear cache after making changes\n";

echo "\n🎯 NEXT ACTIONS:\n";
echo "1. Fix UserResource authorization methods\n";
echo "2. Verify user roles are correct\n";
echo "3. Test with different users\n";

?>
