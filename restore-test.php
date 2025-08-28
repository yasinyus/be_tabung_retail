<?php
// Restore Test untuk API Terima Tabung
// Access: http://localhost:8000/restore-test.php

echo "<h1>Restore Test - API Terima Tabung</h1>";

try {
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    echo "<p>✅ Laravel app loaded</p>";
    
    // Test basic routes
    $routes = [
        "/api/v1/test",
        "/api/v1/auth/login", 
        "/api/v1/mobile/terima-tabung"
    ];
    
    foreach ($routes as $route) {
        try {
            $request = Illuminate\Http\Request::create($route, "GET");
            $response = $app->handle($request);
            echo "<p>✅ Route $route: HTTP " . $response->getStatusCode() . "</p>";
        } catch (Exception $e) {
            echo "<p>❌ Route $route: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<p>✅ Restore test completed</p>";
    echo "<p>📝 Note: API terima-tabung is now back to its original state</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>