<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SECURE 403 DEBUG ===\n\n";

// 1. Check User dengan role
echo "1️⃣  Checking Users and Roles...\n";
try {
    $users = \App\Models\User::all();
    echo "   Total users: " . $users->count() . "\n";
    
    foreach ($users as $user) {
        echo "   - {$user->name} ({$user->email}) - Role: {$user->role}\n";
        
        // Test canAccessPanel
        $canAccess = method_exists($user, 'canAccessPanel') ? 
                    ($user->canAccessPanel(null) ? 'YES' : 'NO') : 'METHOD NOT FOUND';
        echo "     canAccessPanel: {$canAccess}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n";
}

// 2. Check Authentication Guard
echo "\n2️⃣  Checking Auth Guard...\n";
try {
    $guard = config('auth.defaults.guard');
    echo "   Default guard: {$guard}\n";
    
    $webGuard = config('auth.guards.web');
    echo "   Web guard config: " . json_encode($webGuard) . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: {$e->getMessage()}\n";
}

// 3. Test manual login
echo "\n3️⃣  Testing Manual Authentication...\n";
try {
    // Find first admin user
    $adminUser = \App\Models\User::where('role', 'admin_utama')->first();
    
    if ($adminUser) {
        echo "   Found admin: {$adminUser->name}\n";
        
        // Test manual auth
        \Illuminate\Support\Facades\Auth::guard('web')->login($adminUser);
        
        $isAuthenticated = \Illuminate\Support\Facades\Auth::guard('web')->check();
        echo "   Manual auth test: " . ($isAuthenticated ? 'SUCCESS' : 'FAILED') . "\n";
        
        if ($isAuthenticated) {
            $currentUser = \Illuminate\Support\Facades\Auth::guard('web')->user();
            echo "   Current user: {$currentUser->name}\n";
        }
        
    } else {
        echo "   ❌ No admin user found!\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Auth test error: {$e->getMessage()}\n";
}

// 4. Check Filament Panel Configuration
echo "\n4️⃣  Checking Filament Panel...\n";
try {
    // Check if AdminPanelProvider class exists
    if (class_exists('\App\Providers\Filament\AdminPanelProvider')) {
        echo "   ✅ AdminPanelProvider class exists\n";
        
        // Read the provider file to check configuration
        $providerContent = file_get_contents('app/Providers/Filament/AdminPanelProvider.php');
        
        if (strpos($providerContent, '->login()') !== false) {
            echo "   ✅ Login enabled in panel\n";
        } else {
            echo "   ⚠️  Login not found in panel config\n";
        }
        
        if (strpos($providerContent, '->authGuard(') !== false) {
            echo "   ✅ Auth guard configured\n";
        } else {
            echo "   ⚠️  Auth guard not configured\n";
        }
        
        if (strpos($providerContent, 'Authenticate::class') !== false) {
            echo "   ✅ Auth middleware configured\n";
        } else {
            echo "   ⚠️  Auth middleware not found\n";
        }
        
    } else {
        echo "   ❌ AdminPanelProvider class not found\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Panel error: {$e->getMessage()}\n";
}

// 5. Check Routes
echo "\n5️⃣  Checking Admin Routes...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = [];
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'admin')) {
            $adminRoutes[] = $route->uri();
        }
    }
    
    echo "   Total admin routes: " . count($adminRoutes) . "\n";
    if (count($adminRoutes) > 0) {
        echo "   Sample routes:\n";
        foreach (array_slice($adminRoutes, 0, 5) as $route) {
            echo "     - {$route}\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Routes error: {$e->getMessage()}\n";
}

echo "\n🔧 SECURE FIXES TO TRY:\n";
echo "1. Create fresh admin user with proper role\n";
echo "2. Clear browser cookies/cache\n";
echo "3. Test direct login URL\n";
echo "4. Check session configuration\n";
echo "5. Verify .env APP_KEY is set\n";

?>
