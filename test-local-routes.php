<?php
// test-local-routes.php - Test routes in local environment

echo "🧪 TESTING LOCAL ROUTES\n";
echo "======================\n\n";

$baseUrl = 'http://127.0.0.1:8000';

// Test routes
$routes = [
    '/' => 'Home page',
    '/admin' => 'Filament admin panel',
    '/admin/login' => 'Filament login page',
    '/api/v1/auth/login' => 'API login endpoint (OPTIONS)',
    '/up' => 'Health check'
];

foreach ($routes as $route => $description) {
    echo "📍 Testing: $description\n";
    echo "URL: $baseUrl$route\n";
    
    // Use curl to test the route
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $route);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'User-Agent: Test-Agent/1.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ Error: $error\n";
    } elseif ($httpCode >= 200 && $httpCode < 400) {
        echo "✅ Status: $httpCode (OK)\n";
        
        // Check for specific content
        if (strpos($response, '404') !== false) {
            echo "⚠️  Warning: Contains 404 error\n";
        } elseif (strpos($response, 'Filament') !== false) {
            echo "🎯 Filament detected!\n";
        } elseif (strpos($response, 'Laravel') !== false) {
            echo "🎯 Laravel detected!\n";
        }
    } else {
        echo "❌ Status: $httpCode\n";
        if (strpos($response, '404') !== false) {
            echo "❌ 404 Not Found error\n";
        }
    }
    
    echo "\n";
}

echo "🔍 TESTING API ENDPOINTS\n";
echo "========================\n\n";

// Test API with proper method
$apiTests = [
    'OPTIONS /api/v1/auth/login' => ['method' => 'OPTIONS', 'url' => '/api/v1/auth/login'],
    'GET /api/v1/dashboard' => ['method' => 'GET', 'url' => '/api/v1/dashboard'],
];

foreach ($apiTests as $testName => $config) {
    echo "📍 Testing: $testName\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $config['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $config['method']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $httpCode\n";
    if ($httpCode == 401) {
        echo "✅ Authentication required (expected for protected routes)\n";
    } elseif ($httpCode >= 200 && $httpCode < 300) {
        echo "✅ OK\n";
    } elseif ($httpCode == 404) {
        echo "❌ Not Found\n";
    }
    echo "\n";
}

echo "✨ Test completed!\n";
echo "If you see 404 errors, check the web.php routes file.\n";
?>
