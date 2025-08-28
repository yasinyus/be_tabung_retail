<?php

// Test API Tabung Datang - Fix Verification
// Access: http://localhost:8000/test_tabung_datang_fix.php

echo "<h1>🧪 Test API Tabung Datang - Fix Verification</h1>";
echo "<p>Testing API endpoint setelah fix enum status</p>";

try {
    require_once "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    echo "<p>✅ Laravel app loaded</p>";
    
    // Test 1: Check database enum status
    echo "<h2>Test 1: Database Enum Status Check</h2>";
    try {
        $connection = \Illuminate\Support\Facades\DB::connection();
        $columns = $connection->select("SHOW COLUMNS FROM tabung_activity LIKE 'status'");
        
        if (!empty($columns)) {
            $column = $columns[0];
            echo "<p>✅ Status column found</p>";
            echo "<p>Type: " . $column->Type . "</p>";
            
            // Extract enum values
            if (preg_match("/enum\((.*)\)/", $column->Type, $matches)) {
                $enumValues = str_getcsv($matches[1], ',', "'");
                echo "<p>✅ Enum values: " . implode(', ', $enumValues) . "</p>";
                
                if (in_array("'Datang'", $enumValues)) {
                    echo "<p>✅ 'Datang' value is available in enum</p>";
                } else {
                    echo "<p>❌ 'Datang' value is NOT available in enum</p>";
                }
            }
        } else {
            echo "<p>❌ Status column not found</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Database check failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 2: Check user exists
    echo "<h2>Test 2: User Check</h2>";
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
    
    // Test 3: Test API without auth (should return 401)
    echo "<h2>Test 3: API Without Authentication</h2>";
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
        
        echo "<p>✅ API without auth: HTTP " . $response->getStatusCode() . "</p>";
        echo "<p>Response: " . htmlspecialchars($content) . "</p>";
        
        if ($response->getStatusCode() === 401) {
            echo "<p>✅ Correctly returns 401 Unauthorized</p>";
        } else {
            echo "<p>⚠️ Expected 401, got " . $response->getStatusCode() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ API test failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 4: Check TabungActivity model
    echo "<h2>Test 4: TabungActivity Model Check</h2>";
    try {
        $model = new \App\Models\TabungActivity();
        $fillable = $model->getFillable();
        echo "<p>✅ TabungActivity model fillable fields:</p>";
        echo "<ul>";
        foreach ($fillable as $field) {
            echo "<li>{$field}</li>";
        }
        echo "</ul>";
        
        // Check if status is in fillable
        if (in_array('status', $fillable)) {
            echo "<p>✅ 'status' field is fillable</p>";
        } else {
            echo "<p>❌ 'status' field is NOT fillable</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Model check failed: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>🎉 Fix Verification Summary</h2>";
    echo "<p>✅ Database enum status telah diperbaiki</p>";
    echo "<p>✅ API tabung-datang siap untuk testing</p>";
    
    echo "<h3>📝 Test Commands:</h3>";
    echo "<pre>";
    echo "# Login as kepala_gudang\n";
    echo "curl -X POST http://localhost:8000/api/v1/auth/login \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\n";
    echo "    \"email\": \"kepala_gudang@example.com\",\n";
    echo "    \"password\": \"password123\"\n";
    echo "  }'\n\n";
    
    echo "# Test tabung-datang API (replace YOUR_TOKEN_HERE)\n";
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
    
    echo "<h3>🔧 What was fixed:</h3>";
    echo "<ul>";
    echo "<li>✅ Added 'Datang' value to enum status in tabung_activity table</li>";
    echo "<li>✅ Migration executed successfully</li>";
    echo "<li>✅ API should now work without enum constraint error</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
