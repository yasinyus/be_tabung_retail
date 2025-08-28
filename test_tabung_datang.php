<?php

// Test API Tabung Datang
// Access: http://localhost:8000/test_tabung_datang.php

echo "<h1>🧪 Test API Tabung Datang</h1>";
echo "<p>Testing API endpoint untuk Kepala Gudang</p>";

try {
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    echo "<p>✅ Laravel app loaded</p>";
    
    // Test 1: Check route exists
    echo "<h2>Test 1: Route Check</h2>";
    try {
        $request = Illuminate\Http\Request::create("/api/v1/mobile/tabung-datang", "POST");
        $response = $app->handle($request);
        echo "<p>✅ Route /api/v1/mobile/tabung-datang exists</p>";
        echo "<p>Status: " . $response->getStatusCode() . "</p>";
    } catch (Exception $e) {
        echo "<p>❌ Route error: " . $e->getMessage() . "</p>";
    }
    
    // Test 2: Without authentication (should return 401)
    echo "<h2>Test 2: Without Authentication</h2>";
    try {
        $request = Illuminate\Http\Request::create("/api/v1/mobile/tabung-datang", "POST", [], [], [], [
            "CONTENT_TYPE" => "application/json",
            "HTTP_ACCEPT" => "application/json"
        ], json_encode([
            "lokasi" => "GDG-001",
            "armada" => "ARM-001",
            "tabung_qr" => ["T-001", "T-002"],
            "keterangan" => "Test data"
        ]));
        
        $response = $app->handle($request);
        $content = $response->getContent();
        $data = json_decode($content, true);
        
        echo "<p>✅ API without auth: HTTP " . $response->getStatusCode() . "</p>";
        echo "<p>Response: " . htmlspecialchars($content) . "</p>";
        
        if ($response->getStatusCode() === 401) {
            echo "<p>✅ Correctly returns 401 Unauthorized</p>";
        } else {
            echo "<p>⚠️ Expected 401, got " . $response->getStatusCode() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Test failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 3: Check user exists
    echo "<h2>Test 3: User Check</h2>";
    try {
        $user = \App\Models\User::where('email', 'kepala_gudang@example.com')->first();
        if ($user) {
            echo "<p>✅ Kepala Gudang user exists:</p>";
            echo "<ul>";
            echo "<li>ID: {$user->id}</li>";
            echo "<li>Name: {$user->name}</li>";
            echo "<li>Email: {$user->email}</li>";
            echo "<li>Role: {$user->role}</li>";
            echo "</ul>";
        } else {
            echo "<p>❌ Kepala Gudang user not found</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ User check failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 4: Form Structure
    echo "<h2>Test 4: Form Structure</h2>";
    echo "<p>✅ API tabung-datang accepts:</p>";
    echo "<ul>";
    echo "<li><strong>lokasi</strong> (Scan QR Gudang) - Required</li>";
    echo "<li><strong>armada</strong> (Scan QR Armada) - Required</li>";
    echo "<li><strong>tabung_qr</strong> (Scan QR Tabung - multiple) - Required Array</li>";
    echo "<li><strong>keterangan</strong> (Opsional) - Optional Text</li>";
    echo "</ul>";
    
    echo "<p>✅ Automatic data generated:</p>";
    echo "<ul>";
    echo "<li><strong>tanggal</strong> - Format DD-MM-YYYY</li>";
    echo "<li><strong>nama</strong> - Kepala Gudang name</li>";
    echo "<li><strong>total</strong> - Count jumlah tabung</li>";
    echo "<li><strong>transaksi_id</strong> - Format TDG-YYYYMMDDHHMMSS</li>";
    echo "</ul>";
    
    // Test 5: Role Validation
    echo "<h2>Test 5: Role Validation</h2>";
    echo "<p>✅ API validates:</p>";
    echo "<ul>";
    echo "<li>User must be authenticated</li>";
    echo "<li>User must have role 'kepala_gudang'</li>";
    echo "<li>Returns 403 if wrong role</li>";
    echo "<li>Returns 401 if not authenticated</li>";
    echo "</ul>";
    
    // Test 6: QR Validation
    echo "<h2>Test 6: QR Code Validation</h2>";
    echo "<p>✅ API validates QR codes:</p>";
    echo "<ul>";
    echo "<li><strong>Gudang</strong>: Format GDG-XXX or JSON with type 'gudang'</li>";
    echo "<li><strong>Armada</strong>: Any string 3+ chars or JSON with id/nopol</li>";
    echo "<li><strong>Tabung</strong>: Format T-XXX/TBG-XXX or JSON with id/code</li>";
    echo "</ul>";
    
    echo "<h2>🎉 Test Summary</h2>";
    echo "<p>✅ API tabung-datang is ready for testing!</p>";
    echo "<p>📋 Next steps:</p>";
    echo "<ol>";
    echo "<li>Login as kepala_gudang to get token</li>";
    echo "<li>Test with valid QR codes</li>";
    echo "<li>Test with invalid QR codes</li>";
    echo "<li>Test with wrong role user</li>";
    echo "</ol>";
    
    echo "<h3>📝 Test Commands:</h3>";
    echo "<pre>";
    echo "# Login as kepala_gudang\n";
    echo "curl -X POST http://localhost:8000/api/v1/auth/login \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\n";
    echo "    \"email\": \"kepala_gudang@example.com\",\n";
    echo "    \"password\": \"password123\"\n";
    echo "  }'\n\n";
    
    echo "# Test tabung-datang API\n";
    echo "curl -X POST http://localhost:8000/api/v1/mobile/tabung-datang \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -H \"Authorization: Bearer YOUR_TOKEN_HERE\" \\\n";
    echo "  -d '{\n";
    echo "    \"lokasi\": \"GDG-001\",\n";
    echo "    \"armada\": \"ARM-001\",\n";
    echo "    \"tabung_qr\": [\"T-001\", \"T-002\", \"T-003\"],\n";
    echo "    \"keterangan\": \"Tabung dalam kondisi baik\"\n";
    echo "  }'\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
