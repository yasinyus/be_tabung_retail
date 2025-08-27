<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUG AUTHENTICATION BYPASS ===\n\n";

// 1. Check current authentication state
echo "1️⃣  Current Auth State...\n";
try {
    $isAuthenticated = \Illuminate\Support\Facades\Auth::guard('web')->check();
    echo "   Web guard authenticated: " . ($isAuthenticated ? 'YES' : 'NO') . "\n";
    
    if ($isAuthenticated) {
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        echo "   Current user: {$user->name} ({$user->email})\n";
        echo "   User role: {$user->role}\n";
        echo "   Can access panel: " . ($user->canAccessPanel(null) ? 'YES' : 'NO') . "\n";
    } else {
        echo "   No authenticated user in web guard\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Auth check error: {$e->getMessage()}\n";
}

// 2. Check Filament panel configuration di runtime
echo "\n2️⃣  Runtime Panel Configuration...\n";
try {
    // Get Filament instance
    $panels = \Filament\Facades\Filament::getPanels();
    
    foreach ($panels as $panelId => $panel) {
        echo "   Panel ID: {$panelId}\n";
        echo "   Panel path: " . $panel->getPath() . "\n";
        
        // Check if panel has login enabled
        $hasLogin = method_exists($panel, 'hasLogin') ? $panel->hasLogin() : 'Unknown';
        echo "   Has login: " . ($hasLogin ? 'YES' : 'NO') . "\n";
        
        // Check auth guard
        $authGuard = method_exists($panel, 'getAuthGuard') ? $panel->getAuthGuard() : 'Unknown';
        echo "   Auth guard: {$authGuard}\n";
        
        // Check middleware
        $middleware = method_exists($panel, 'getMiddleware') ? count($panel->getMiddleware()) : 'Unknown';
        echo "   Middleware count: {$middleware}\n";
        
        $authMiddleware = method_exists($panel, 'getAuthMiddleware') ? count($panel->getAuthMiddleware()) : 'Unknown';
        echo "   Auth middleware count: {$authMiddleware}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Panel config error: {$e->getMessage()}\n";
}

// 3. Check routes yang actually ter-register
echo "\n3️⃣  Registered Routes Analysis...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = [];
    $loginRoutes = [];
    $authRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        $middleware = $route->middleware();
        
        if (str_contains($uri, 'admin')) {
            $adminRoutes[] = [
                'uri' => $uri,
                'middleware' => $middleware,
                'hasAuth' => in_array('auth', $middleware) || in_array('filament.auth', $middleware)
            ];
        }
        
        if (str_contains($uri, 'login')) {
            $loginRoutes[] = $uri;
        }
        
        if (in_array('auth', $middleware) || in_array('filament.auth', $middleware)) {
            $authRoutes[] = $uri;
        }
    }
    
    echo "   Total admin routes: " . count($adminRoutes) . "\n";
    echo "   Login routes: " . count($loginRoutes) . "\n";
    echo "   Routes with auth middleware: " . count($authRoutes) . "\n";
    
    echo "\n   Admin routes detail:\n";
    foreach (array_slice($adminRoutes, 0, 5) as $route) {
        $hasAuth = $route['hasAuth'] ? 'AUTH' : 'NO AUTH';
        echo "     - {$route['uri']} [{$hasAuth}]\n";
    }
    
    if (!empty($loginRoutes)) {
        echo "\n   Login routes:\n";
        foreach ($loginRoutes as $route) {
            echo "     - {$route}\n";
        }
    } else {
        echo "\n   ❌ NO LOGIN ROUTES FOUND!\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Routes analysis error: {$e->getMessage()}\n";
}

// 4. Check if ada custom auth bypass
echo "\n4️⃣  Checking Potential Auth Bypass...\n";

// Check AdminPanelProvider content for any bypass
$providerContent = file_get_contents('app/Providers/Filament/AdminPanelProvider.php');

$suspiciousPatterns = [
    '->authMiddleware([])' => 'Empty auth middleware',
    '->login(false)' => 'Login disabled',
    'return true' => 'Possible bypass in canAccessPanel',
    '->canAccess(' => 'Custom access check',
];

foreach ($suspiciousPatterns as $pattern => $description) {
    if (strpos($providerContent, $pattern) !== false) {
        echo "   ⚠️  FOUND: {$description}\n";
    } else {
        echo "   ✅ Safe: No {$description}\n";
    }
}

echo "\n🔧 POSSIBLE ISSUES:\n";
echo "1. Middleware not being applied to routes\n";
echo "2. Auth guard configuration mismatch\n";
echo "3. Session not working properly\n";
echo "4. Filament panel configuration override\n";
echo "5. Cache issue - old configuration still active\n";

echo "\n🛡️  SECURITY RISK LEVEL: HIGH\n";
echo "Admin panel accessible without authentication!\n";

?>
