<?php
// simple-qr-generator.php - Simple QR generator without Laravel bootstrap

echo "🚀 Simple QR Code Generator (No Laravel Bootstrap)\n";
echo "================================================\n\n";

// Only require autoload
require_once 'vendor/autoload.php';

use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Manual configuration for QrCode
if (!class_exists('QrCode')) {
    echo "❌ QrCode class not available\n";
    exit;
}

echo "✅ QrCode library loaded\n";

// Create directories manually
$directories = [
    'storage/app/public/qr_codes/tabung',
    'storage/app/public/qr_codes/armada',
    'storage/app/public/qr_codes/gudang',
    'storage/app/public/qr_codes/pelanggan'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// Function to generate QR code without database
function generateSimpleQR($type, $id, $data) {
    try {
        $qrData = json_encode($data);
        $qrCode = QrCode::size(200)->margin(1)->format('svg')->generate($qrData);
        
        $filename = $type . '_' . $id . '.svg';
        $path = 'storage/app/public/qr_codes/' . $type . '/' . $filename;
        
        file_put_contents($path, $qrCode);
        
        echo "✅ Generated: $filename\n";
        return true;
    } catch (Exception $e) {
        echo "❌ Error generating $type $id: " . $e->getMessage() . "\n";
        return false;
    }
}

// Generate sample QR codes (you can modify IDs based on your data)
echo "\n🏷️ Generating sample Tabung QR codes...\n";
for ($i = 1; $i <= 10; $i++) {
    $data = [
        'type' => 'tabung',
        'id' => $i,
        'kode_tabung' => 'TBG-' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'url' => 'https://test.gasalamsolusi.my.id/tabung/' . $i
    ];
    generateSimpleQR('tabung', $i, $data);
}

echo "\n🚛 Generating sample Armada QR codes...\n";
for ($i = 1; $i <= 10; $i++) {
    $data = [
        'type' => 'armada',
        'id' => $i,
        'nopol' => 'B' . (1000 + $i) . 'ABC',
        'url' => 'https://test.gasalamsolusi.my.id/armada/' . $i
    ];
    generateSimpleQR('armada', $i, $data);
}

echo "\n🏢 Generating sample Gudang QR codes...\n";
for ($i = 1; $i <= 10; $i++) {
    $data = [
        'type' => 'gudang',
        'id' => $i,
        'kode_gudang' => 'GDG-' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'url' => 'https://test.gasalamsolusi.my.id/gudang/' . $i
    ];
    generateSimpleQR('gudang', $i, $data);
}

echo "\n👥 Generating sample Pelanggan QR codes...\n";
for ($i = 1; $i <= 10; $i++) {
    $data = [
        'type' => 'pelanggan',
        'id' => $i,
        'kode_pelanggan' => 'PLG-' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'url' => 'https://test.gasalamsolusi.my.id/pelanggan/' . $i
    ];
    generateSimpleQR('pelanggan', $i, $data);
}

echo "\n🎉 Simple QR generation completed!\n";
echo "📁 QR codes saved to storage/app/public/qr_codes/\n";
echo "🌐 Access via: https://test.gasalamsolusi.my.id/storage/qr_codes/\n";
echo "\n💡 Note: This generates sample QR codes with default data.\n";
echo "   For actual data, use qr-generator.php after fixing Laravel bootstrap.\n";
?>
