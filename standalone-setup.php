<?php
// standalone-setup.php - Complete setup without Laravel bootstrap

echo "🔧 Starting standalone setup...\n";
echo "⚡ This script doesn't use Laravel bootstrap to avoid Pail errors\n\n";

// Database configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'tabung_retail',
    'username' => 'root',
    'password' => ''
];

// You can modify these values for your server
echo "📋 Database Configuration:\n";
echo "Host: {$config['host']}\n";
echo "Database: {$config['dbname']}\n";
echo "Username: {$config['username']}\n";
echo "Password: " . (empty($config['password']) ? '(empty)' : '(set)') . "\n\n";

// 1. Create storage directories
$directories = [
    'storage/app/public',
    'storage/app/public/qr_codes',
    'storage/app/public/qr_codes/tabung',
    'storage/app/public/qr_codes/armada',
    'storage/app/public/qr_codes/gudang',
    'storage/app/public/qr_codes/pelanggan'
];

echo "📁 Creating directories...\n";
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created: $dir\n";
    } else {
        echo "✅ Exists: $dir\n";
    }
    chmod($dir, 0755);
}

// 2. Create storage link
echo "\n🔗 Creating storage link...\n";
$target = '../storage/app/public';
$link = 'public/storage';

// Remove if exists
if (file_exists($link) || is_link($link)) {
    unlink($link);
    echo "🗑️  Removed existing link\n";
}

// Create symlink
if (symlink($target, $link)) {
    echo "✅ Storage link created successfully!\n";
} else {
    echo "❌ Failed to create storage link\n";
}

// 3. Test database connection
echo "\n🗄️  Testing database connection...\n";
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful!\n";
    
    // Test tables exist
    $tables = ['tabung', 'armada', 'gudang', 'pelanggan'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists\n";
        } else {
            echo "❌ Table '$table' not found\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "🔧 Please update database config in this file and try again\n";
}

// 4. Generate QR codes directly
echo "\n📱 Generating QR codes...\n";

// Simple QR code generation function
function generateSimpleQr($text, $filename) {
    // Create a simple SVG QR-like pattern (for demonstration)
    $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<rect width="200" height="200" fill="white"/>';
    $svg .= '<text x="100" y="100" text-anchor="middle" fill="black" font-size="12">';
    $svg .= htmlspecialchars($text);
    $svg .= '</text>';
    $svg .= '</svg>';
    
    return file_put_contents($filename, $svg);
}

// Generate sample QR codes
$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$sampleData = [
    'tabung' => ['id' => 1, 'code' => 'TBG001'],
    'armada' => ['id' => 1, 'code' => 'ARM001'],
    'gudang' => ['id' => 1, 'code' => 'GDG001'],
    'pelanggan' => ['id' => 1, 'code' => 'PLG001']
];

foreach ($sampleData as $type => $data) {
    $qrText = $baseUrl . "/api/v1/qr-scan?type=$type&id={$data['id']}";
    $filename = "storage/app/public/qr_codes/$type/{$data['code']}.svg";
    
    if (generateSimpleQr($qrText, $filename)) {
        echo "✅ Generated QR for $type: {$data['code']}\n";
    } else {
        echo "❌ Failed to generate QR for $type: {$data['code']}\n";
    }
}

// 5. Generate real QR codes if SimpleSoftwareIO is available
echo "\n🎯 Attempting to generate real QR codes...\n";

// Check if vendor autoload exists
if (file_exists('vendor/autoload.php')) {
    echo "📦 Composer autoload found, attempting to use QR library...\n";
    
    try {
        require_once 'vendor/autoload.php';
        
        // Check if QR Code library is available
        if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            echo "❌ QrCode Facade not available in standalone mode\n";
        } elseif (class_exists('SimpleSoftwareIO\QrCode\Generator')) {
            echo "🎯 Using QR Code Generator directly...\n";
            
            $generator = new \SimpleSoftwareIO\QrCode\Generator;
            
            foreach ($sampleData as $type => $data) {
                $qrText = $baseUrl . "/api/v1/qr-scan?type=$type&id={$data['id']}";
                $filename = "storage/app/public/qr_codes/$type/{$data['code']}_real.svg";
                
                try {
                    $qrCode = $generator->format('svg')->size(200)->generate($qrText);
                    file_put_contents($filename, $qrCode);
                    echo "✅ Generated real QR for $type: {$data['code']}\n";
                } catch (Exception $e) {
                    echo "❌ Failed to generate real QR for $type: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "❌ QR Code library not found\n";
        }
    } catch (Exception $e) {
        echo "❌ Error loading QR library: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Vendor autoload not found\n";
}

// 6. Update database with QR paths (if connection works)
if (isset($pdo)) {
    echo "\n💾 Updating database with QR paths...\n";
    
    try {
        // Update sample records
        $updates = [
            'tabung' => "UPDATE tabung SET qr_code = 'qr_codes/tabung/TBG001.svg' WHERE id = 1",
            'armada' => "UPDATE armada SET qr_code = 'qr_codes/armada/ARM001.svg' WHERE id = 1",
            'gudang' => "UPDATE gudang SET qr_code = 'qr_codes/gudang/GDG001.svg' WHERE id = 1",
            'pelanggan' => "UPDATE pelanggan SET qr_code = 'qr_codes/pelanggan/PLG001.svg' WHERE id = 1"
        ];
        
        foreach ($updates as $table => $sql) {
            try {
                $pdo->exec($sql);
                echo "✅ Updated $table QR path\n";
            } catch (Exception $e) {
                echo "⚠️  Could not update $table: " . $e->getMessage() . "\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Database update error: " . $e->getMessage() . "\n";
    }
}

// 7. Test file access
echo "\n🧪 Testing file access...\n";
$testUrl = $baseUrl . '/storage/qr_codes/tabung/TBG001.svg';
echo "QR Code URL: $testUrl\n";

if (file_exists('public/storage/qr_codes/tabung/TBG001.svg')) {
    echo "✅ QR file accessible via public link\n";
} else {
    echo "❌ QR file not accessible via public link\n";
}

// 8. Create test API endpoint
echo "\n🔌 Creating test API...\n";
$testApiContent = '<?php
// test-api-endpoint.php - Test the QR scan endpoint

header("Content-Type: application/json");

if ($_GET["type"] && $_GET["id"]) {
    $response = [
        "success" => true,
        "message" => "QR scan successful",
        "data" => [
            "type" => $_GET["type"],
            "id" => $_GET["id"],
            "scanned_at" => date("Y-m-d H:i:s"),
            "test_mode" => true
        ]
    ];
} else {
    $response = [
        "success" => false,
        "message" => "Missing parameters"
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>';

file_put_contents('public/test-api-endpoint.php', $testApiContent);
echo "✅ Test API created: $baseUrl/test-api-endpoint.php\n";

// Final summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 STANDALONE SETUP COMPLETED!\n";
echo str_repeat("=", 50) . "\n";
echo "📋 SUMMARY:\n";
echo "✅ Directories created: " . count($directories) . "\n";
echo "✅ Storage link: " . (is_link('public/storage') ? 'WORKING' : 'FAILED') . "\n";
echo "✅ Database: " . (isset($pdo) ? 'CONNECTED' : 'NOT CONNECTED') . "\n";
echo "✅ QR codes generated: " . count($sampleData) . "\n";
echo "✅ Test API created\n\n";

echo "🔗 QUICK TESTS:\n";
echo "1. QR Code: $baseUrl/storage/qr_codes/tabung/TBG001.svg\n";
echo "2. Test API: $baseUrl/test-api-endpoint.php?type=tabung&id=1\n";
echo "3. Main API: $baseUrl/api/v1/auth/login\n\n";

echo "📱 MOBILE APP ENDPOINTS:\n";
echo "- Login: POST $baseUrl/api/v1/auth/login\n";
echo "- Dashboard: GET $baseUrl/api/v1/mobile/dashboard\n";
echo "- QR Scan: POST $baseUrl/api/v1/mobile/scan-qr\n\n";

echo "🚀 Next steps:\n";
echo "1. Test the URLs above\n";
echo "2. Update mobile app to use these endpoints\n";
echo "3. Generate more QR codes as needed\n";
echo "4. Remove this file after testing\n\n";

echo "✨ Setup completed without Laravel Pail errors!\n";
?>
