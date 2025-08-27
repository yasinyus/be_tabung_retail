<?php
// Quick server check
try {
    require_once "../vendor/autoload.php";
    $app = require_once "../bootstrap/app.php";
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "<h2>✅ Server Status: OK</h2>";
    echo "<p>Laravel: Working</p>";
    
    $panels = \Filament\Facades\Filament::getPanels();
    echo "<p>Filament Panels: " . count($panels) . "</p>";
    
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $adminRoutes = 0;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'admin')) {
            $adminRoutes++;
        }
    }
    echo "<p>Admin Routes: {$adminRoutes}</p>";
    
    $users = \App\Models\User::count();
    echo "<p>Users: {$users}</p>";
    
    echo "<p><a href='/admin'>Try Admin Panel</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
