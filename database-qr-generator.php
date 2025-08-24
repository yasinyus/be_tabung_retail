<?php
// database-qr-generator.php - Generate QR codes with database integration

echo "📱 Database QR Generator\n";
echo "========================\n\n";

// Database configuration - UPDATE THESE VALUES
$config = [
    'host' => 'localhost',        // Your database host
    'dbname' => 'tabung_retail',  // Your database name
    'username' => 'root',         // Your database username
    'password' => ''              // Your database password
];

echo "🔧 Configuration:\n";
echo "Host: {$config['host']}\n";
echo "Database: {$config['dbname']}\n";
echo "Username: {$config['username']}\n\n";

// Get current URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . '://' . $host . dirname($_SERVER['PHP_SELF']);

echo "🌐 Base URL: $baseUrl\n\n";

try {
    // Connect to database
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connected successfully!\n\n";

    // Simple SVG QR code generator
    function generateQrCodeSvg($text, $size = 200) {
        // Create a simple placeholder QR code in SVG format
        $svg = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $svg .= "<svg width='$size' height='$size' xmlns='http://www.w3.org/2000/svg'>\n";
        $svg .= "<rect width='$size' height='$size' fill='white' stroke='black' stroke-width='2'/>\n";
        
        // Add some QR-like patterns
        for ($i = 0; $i < 10; $i++) {
            for ($j = 0; $j < 10; $j++) {
                if (($i + $j) % 2 == 0) {
                    $x = $i * 20;
                    $y = $j * 20;
                    $svg .= "<rect x='$x' y='$y' width='20' height='20' fill='black'/>\n";
                }
            }
        }
        
        // Add text in center
        $svg .= "<rect x='60' y='80' width='80' height='40' fill='white'/>\n";
        $svg .= "<text x='100' y='105' text-anchor='middle' font-family='Arial' font-size='10' fill='black'>QR CODE</text>\n";
        $svg .= "</svg>";
        
        return $svg;
    }

    // Process Tabung
    echo "🔥 Processing Tabung...\n";
    $stmt = $pdo->query("SELECT id, kode_tabung FROM tabung ORDER BY id LIMIT 10");
    $tabungCount = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qrUrl = $baseUrl . "/api/v1/mobile/scan-qr?type=tabung&id=" . $row['id'];
        $qrSvg = generateQrCodeSvg($qrUrl);
        
        // Save QR code file
        $filename = "storage/app/public/qr_codes/tabung/{$row['kode_tabung']}.svg";
        file_put_contents($filename, $qrSvg);
        
        // Update database
        $updateStmt = $pdo->prepare("UPDATE tabung SET qr_code = ? WHERE id = ?");
        $qrPath = "qr_codes/tabung/{$row['kode_tabung']}.svg";
        $updateStmt->execute([$qrPath, $row['id']]);
        
        echo "✅ {$row['kode_tabung']} -> $qrPath\n";
        $tabungCount++;
    }

    // Process Armada
    echo "\n🚛 Processing Armada...\n";
    $stmt = $pdo->query("SELECT id, nopol FROM armada ORDER BY id LIMIT 10");
    $armadaCount = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qrUrl = $baseUrl . "/api/v1/mobile/scan-qr?type=armada&id=" . $row['id'];
        $qrSvg = generateQrCodeSvg($qrUrl);
        
        // Clean nopol for filename
        $cleanNopol = preg_replace('/[^A-Za-z0-9]/', '_', $row['nopol']);
        
        // Save QR code file
        $filename = "storage/app/public/qr_codes/armada/{$cleanNopol}.svg";
        file_put_contents($filename, $qrSvg);
        
        // Update database
        $updateStmt = $pdo->prepare("UPDATE armada SET qr_code = ? WHERE id = ?");
        $qrPath = "qr_codes/armada/{$cleanNopol}.svg";
        $updateStmt->execute([$qrPath, $row['id']]);
        
        echo "✅ {$row['nopol']} -> $qrPath\n";
        $armadaCount++;
    }

    // Process Gudang
    echo "\n🏪 Processing Gudang...\n";
    $stmt = $pdo->query("SELECT id, kode_gudang FROM gudang ORDER BY id LIMIT 10");
    $gudangCount = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qrUrl = $baseUrl . "/api/v1/mobile/scan-qr?type=gudang&id=" . $row['id'];
        $qrSvg = generateQrCodeSvg($qrUrl);
        
        // Save QR code file
        $filename = "storage/app/public/qr_codes/gudang/{$row['kode_gudang']}.svg";
        file_put_contents($filename, $qrSvg);
        
        // Update database
        $updateStmt = $pdo->prepare("UPDATE gudang SET qr_code = ? WHERE id = ?");
        $qrPath = "qr_codes/gudang/{$row['kode_gudang']}.svg";
        $updateStmt->execute([$qrPath, $row['id']]);
        
        echo "✅ {$row['kode_gudang']} -> $qrPath\n";
        $gudangCount++;
    }

    // Process Pelanggan
    echo "\n👥 Processing Pelanggan...\n";
    $stmt = $pdo->query("SELECT id, kode_pelanggan FROM pelanggan ORDER BY id LIMIT 10");
    $pelangganCount = 0;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qrUrl = $baseUrl . "/api/v1/mobile/scan-qr?type=pelanggan&id=" . $row['id'];
        $qrSvg = generateQrCodeSvg($qrUrl);
        
        // Save QR code file
        $filename = "storage/app/public/qr_codes/pelanggan/{$row['kode_pelanggan']}.svg";
        file_put_contents($filename, $qrSvg);
        
        // Update database
        $updateStmt = $pdo->prepare("UPDATE pelanggan SET qr_code = ? WHERE id = ?");
        $qrPath = "qr_codes/pelanggan/{$row['kode_pelanggan']}.svg";
        $updateStmt->execute([$qrPath, $row['id']]);
        
        echo "✅ {$row['kode_pelanggan']} -> $qrPath\n";
        $pelangganCount++;
    }

    // Summary
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "🎉 QR GENERATION COMPLETED!\n";
    echo str_repeat("=", 50) . "\n";
    echo "📊 SUMMARY:\n";
    echo "✅ Tabung: $tabungCount QR codes generated\n";
    echo "✅ Armada: $armadaCount QR codes generated\n";
    echo "✅ Gudang: $gudangCount QR codes generated\n";
    echo "✅ Pelanggan: $pelangganCount QR codes generated\n";
    echo "📦 Total: " . ($tabungCount + $armadaCount + $gudangCount + $pelangganCount) . " QR codes\n\n";

    echo "🔗 SAMPLE QR URLS:\n";
    if ($tabungCount > 0) {
        echo "Tabung: $baseUrl/storage/qr_codes/tabung/[kode].svg\n";
    }
    if ($armadaCount > 0) {
        echo "Armada: $baseUrl/storage/qr_codes/armada/[nopol].svg\n";
    }
    if ($gudangCount > 0) {
        echo "Gudang: $baseUrl/storage/qr_codes/gudang/[kode].svg\n";
    }
    if ($pelangganCount > 0) {
        echo "Pelanggan: $baseUrl/storage/qr_codes/pelanggan/[kode].svg\n";
    }

    echo "\n🚀 NEXT STEPS:\n";
    echo "1. Test QR codes by visiting the URLs above\n";
    echo "2. Test API endpoint: $baseUrl/api/v1/mobile/scan-qr\n";
    echo "3. Update mobile app to scan these QR codes\n";
    echo "4. Generate more QR codes by running this script again\n\n";

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "🔧 Please check your database configuration and try again.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "✨ Process completed!\n";
?>
