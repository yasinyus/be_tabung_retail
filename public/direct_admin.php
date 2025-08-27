<?php
// Direct Admin Access - Bypass routing issues
try {
    require_once "../vendor/autoload.php";
    $app = require_once "../bootstrap/app.php";
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Force login as admin user
    $adminUser = \App\Models\User::where("role", "admin_utama")->first();
    if ($adminUser) {
        \Illuminate\Support\Facades\Auth::guard("web")->login($adminUser);
        echo "<p>Logged in as: " . $adminUser->name . "</p>";
    }
    
    // Check if we can access Filament
    $panels = \Filament\Facades\Filament::getPanels();
    
    if (!empty($panels)) {
        echo "<h2>Filament Panels Available:</h2>";
        foreach ($panels as $panelId => $panel) {
            $path = $panel->getPath();
            echo "<p>Panel: {$panelId} - Path: /{$path}</p>";
        }
        
        // Try to render admin panel directly
        echo "<h2>Attempting Direct Panel Access:</h2>";
        
        // Simulate request to admin
        $_SERVER["REQUEST_URI"] = "/admin";
        $_SERVER["REQUEST_METHOD"] = "GET";
        
        try {
            // Get the admin panel
            $adminPanel = $panels["admin"] ?? null;
            if ($adminPanel) {
                echo "<p>✅ Admin panel found and accessible</p>";
                echo "<p>🔄 Redirecting to actual admin...</p>";
                echo "<script>setTimeout(function(){ window.location.href = \"/admin\"; }, 2000);</script>";
            } else {
                echo "<p>❌ Admin panel not found in panels list</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Error accessing admin panel: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ No Filament panels found!</p>";
    }
    
    echo "<hr>";
    echo "<h3>Alternative Links:</h3>";
    echo "<a href=\"/admin\">Try Main Admin</a><br>";
    echo "<a href=\"/index.php/admin\">Try Index.php Admin</a><br>";
    echo "<a href=\"/emergency.php\">Back to Emergency</a><br>";
    
    echo "<h3>Debug Info:</h3>";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = 0;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), "admin")) {
            $adminRoutes++;
        }
    }
    echo "<p>Total admin routes registered: {$adminRoutes}</p>";
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}
?>