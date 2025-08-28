<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\User;

echo "Testing API with authentication...\n";

try {
    $app = require_once 'bootstrap/app.php';
    echo "✅ Laravel application loaded successfully\n";
    
    // Test login first
    echo "Testing login...\n";
    
    $loginRequest = \Illuminate\Http\Request::create('/api/v1/auth/login', 'POST', [], [], [], [], json_encode([
        'email' => 'admin@example.com',
        'password' => 'password'
    ]));
    $loginRequest->headers->set('Accept', 'application/json');
    $loginRequest->headers->set('Content-Type', 'application/json');
    
    $loginResponse = $app->handle($loginRequest);
    echo "Login Response Status: " . $loginResponse->getStatusCode() . "\n";
    echo "Login Response Content: " . $loginResponse->getContent() . "\n";
    
    if ($loginResponse->getStatusCode() === 200) {
        $loginData = json_decode($loginResponse->getContent(), true);
        $token = $loginData['token'] ?? null;
        
        if ($token) {
            echo "✅ Login successful, token obtained\n";
            
            // Test terima-tabung with token
            echo "Testing terima-tabung with auth...\n";
            
            $request = \Illuminate\Http\Request::create('/api/v1/mobile/terima-tabung', 'POST', [], [], [], [], json_encode([
                'lokasi_qr' => 'GDG-001',
                'armada_qr' => 'ARM-001',
                'tabung_qr' => ['T-001', 'T-002'],
                'keterangan' => 'Test terima tabung'
            ]));
            $request->headers->set('Accept', 'application/json');
            $request->headers->set('Content-Type', 'application/json');
            $request->headers->set('Authorization', 'Bearer ' . $token);
            
            $response = $app->handle($request);
            
            echo "Terima-tabung Response Status: " . $response->getStatusCode() . "\n";
            echo "Terima-tabung Response Content: " . $response->getContent() . "\n";
        } else {
            echo "❌ No token in login response\n";
        }
    } else {
        echo "❌ Login failed\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
