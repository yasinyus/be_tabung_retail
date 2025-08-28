<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

echo "Testing API endpoint terima-tabung...\n";

try {
    $app = require_once 'bootstrap/app.php';
    echo "✅ Laravel application loaded successfully\n";
    
    // Test API endpoint without authentication (should return 401)
    echo "Testing /api/v1/mobile/terima-tabung without auth...\n";
    
    // Create a test request
    $request = \Illuminate\Http\Request::create('/api/v1/mobile/terima-tabung', 'POST');
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/json');
    
    // Add test data
    $request->merge([
        'lokasi_qr' => 'GDG-001',
        'armada_qr' => 'ARM-001',
        'tabung_qr' => ['T-001', 'T-002'],
        'keterangan' => 'Test terima tabung'
    ]);
    
    $response = $app->handle($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
