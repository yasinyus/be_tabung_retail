<?php
// test-api.php - Test API endpoints manual

echo "🧪 API Testing Script\n";
echo "====================\n\n";

$baseUrl = 'https://test.gasalamsolusi.my.id';

// Function to make HTTP request
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }
    
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'response' => $response,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

// Test 1: Basic API
echo "1️⃣ Testing basic API endpoint...\n";
$result = makeRequest($baseUrl . '/api/test');
if ($result['http_code'] == 200) {
    echo "✅ API test endpoint: OK\n";
} else {
    echo "❌ API test endpoint failed: HTTP {$result['http_code']}\n";
}

// Test 2: Universal Login
echo "\n2️⃣ Testing universal login (no role parameter)...\n";
$loginData = json_encode([
    'email' => 'driver@gmail.com',
    'password' => 'password'
]);

$headers = ['Content-Type: application/json'];
$result = makeRequest($baseUrl . '/api/v1/auth/login', 'POST', $loginData, $headers);

if ($result['http_code'] == 200) {
    $response = json_decode($result['response'], true);
    if (isset($response['token'])) {
        $token = $response['token'];
        echo "✅ Login successful\n";
        echo "   User: " . $response['user']['name'] . "\n";
        echo "   Role: " . $response['user']['role'] . "\n";
        echo "   Token: " . substr($token, 0, 20) . "...\n";
        
        // Test 3: QR Scan with token
        echo "\n3️⃣ Testing QR scan endpoint...\n";
        $scanData = json_encode([
            'type' => 'tabung',
            'id' => 1
        ]);
        
        $authHeaders = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ];
        
        $scanResult = makeRequest($baseUrl . '/api/v1/scan-qr', 'POST', $scanData, $authHeaders);
        
        if ($scanResult['http_code'] == 200) {
            $scanResponse = json_decode($scanResult['response'], true);
            echo "✅ QR scan successful\n";
            echo "   Type: " . $scanResponse['data']['type'] . "\n";
            echo "   Code: " . $scanResponse['data']['kode_tabung'] . "\n";
        } else {
            echo "❌ QR scan failed: HTTP {$scanResult['http_code']}\n";
            echo "   Response: " . $scanResult['response'] . "\n";
        }
        
        // Test 4: Dashboard
        echo "\n4️⃣ Testing dashboard endpoint...\n";
        $dashResult = makeRequest($baseUrl . '/api/v1/dashboard', 'GET', null, [
            'Authorization: Bearer ' . $token
        ]);
        
        if ($dashResult['http_code'] == 200) {
            echo "✅ Dashboard accessible\n";
        } else {
            echo "❌ Dashboard failed: HTTP {$dashResult['http_code']}\n";
        }
        
    } else {
        echo "❌ Login failed: No token in response\n";
        echo "   Response: " . $result['response'] . "\n";
    }
} else {
    echo "❌ Login failed: HTTP {$result['http_code']}\n";
    echo "   Response: " . $result['response'] . "\n";
}

// Test 5: QR Code File Access
echo "\n5️⃣ Testing QR code file access...\n";
$qrResult = makeRequest($baseUrl . '/storage/qr_codes/tabung/tabung_1.svg');
if ($qrResult['http_code'] == 200) {
    echo "✅ QR code file accessible\n";
} else {
    echo "❌ QR code file not accessible: HTTP {$qrResult['http_code']}\n";
}

// Test 6: Storage Link Test
echo "\n6️⃣ Testing storage link...\n";
$testResult = makeRequest($baseUrl . '/storage/test.txt');
if ($testResult['http_code'] == 200) {
    echo "✅ Storage link working\n";
} else {
    echo "❌ Storage link not working: HTTP {$testResult['http_code']}\n";
}

echo "\n";
echo "🏁 API Testing Complete!\n";
echo "========================\n";
echo "Next steps:\n";
echo "1. Fix any failed tests\n";
echo "2. Test with mobile app\n";
echo "3. Remove test files (setup.php, qr-generator.php, test-api.php)\n";
echo "\n";
?>
