<?php
// qr-generator.php - Manual QR code generator

echo "🚀 Starting QR code generation...\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Autoload composer
if (!file_exists('vendor/autoload.php')) {
    echo "❌ vendor/autoload.php not found. Please run composer install first.\n";
    exit;
}

require_once 'vendor/autoload.php';

// Bootstrap Laravel manually
try {
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    echo "✅ Laravel bootstrapped successfully\n";
} catch (Exception $e) {
    echo "❌ Bootstrap error: " . $e->getMessage() . "\n";
    exit;
}

use App\Models\Tabung;
use App\Models\Armada;
use App\Models\Gudang;
use App\Models\Pelanggan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Function to generate QR code
function generateQR($model, $data, $type, $id) {
    try {
        $qrData = json_encode($data);
        $qrCode = QrCode::size(200)->margin(1)->format('svg')->generate($qrData);
        
        $filename = $type . '_' . $id . '.svg';
        $path = 'qr_codes/' . $type . '/' . $filename;
        $fullPath = 'storage/app/public/' . $path;
        
        // Ensure directory exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($fullPath, $qrCode);
        
        // Update model
        $model->update(['qr_code' => $path]);
        
        echo "✅ Generated QR for {$type} ID {$id} - {$filename}\n";
        return true;
    } catch (Exception $e) {
        echo "❌ Error generating QR for {$type} ID {$id}: " . $e->getMessage() . "\n";
        return false;
    }
}

$totalGenerated = 0;
$totalErrors = 0;

// Generate for Tabung
echo "\n🏷️ Processing Tabung...\n";
try {
    $tabungs = Tabung::all();
    echo "Found {$tabungs->count()} tabung records\n";
    
    foreach ($tabungs as $tabung) {
        $data = [
            'type' => 'tabung',
            'id' => $tabung->id,
            'kode_tabung' => $tabung->kode_tabung,
            'seri_tabung' => $tabung->seri_tabung,
            'url' => 'https://test.gasalamsolusi.my.id/tabung/' . $tabung->id
        ];
        
        if (generateQR($tabung, $data, 'tabung', $tabung->id)) {
            $totalGenerated++;
        } else {
            $totalErrors++;
        }
    }
} catch (Exception $e) {
    echo "❌ Error processing Tabung: " . $e->getMessage() . "\n";
}

// Generate for Armada
echo "\n🚛 Processing Armada...\n";
try {
    $armadas = Armada::all();
    echo "Found {$armadas->count()} armada records\n";
    
    foreach ($armadas as $armada) {
        $data = [
            'type' => 'armada',
            'id' => $armada->id,
            'nopol' => $armada->nopol,
            'kapasitas' => $armada->kapasitas,
            'url' => 'https://test.gasalamsolusi.my.id/armada/' . $armada->id
        ];
        
        if (generateQR($armada, $data, 'armada', $armada->id)) {
            $totalGenerated++;
        } else {
            $totalErrors++;
        }
    }
} catch (Exception $e) {
    echo "❌ Error processing Armada: " . $e->getMessage() . "\n";
}

// Generate for Gudang
echo "\n🏢 Processing Gudang...\n";
try {
    $gudangs = Gudang::all();
    echo "Found {$gudangs->count()} gudang records\n";
    
    foreach ($gudangs as $gudang) {
        $data = [
            'type' => 'gudang',
            'id' => $gudang->id,
            'kode_gudang' => $gudang->kode_gudang,
            'nama_gudang' => $gudang->nama_gudang,
            'url' => 'https://test.gasalamsolusi.my.id/gudang/' . $gudang->id
        ];
        
        if (generateQR($gudang, $data, 'gudang', $gudang->id)) {
            $totalGenerated++;
        } else {
            $totalErrors++;
        }
    }
} catch (Exception $e) {
    echo "❌ Error processing Gudang: " . $e->getMessage() . "\n";
}

// Generate for Pelanggan
echo "\n👥 Processing Pelanggan...\n";
try {
    $pelanggans = Pelanggan::all();
    echo "Found {$pelanggans->count()} pelanggan records\n";
    
    foreach ($pelanggans as $pelanggan) {
        $data = [
            'type' => 'pelanggan',
            'id' => $pelanggan->id,
            'kode_pelanggan' => $pelanggan->kode_pelanggan,
            'nama_pelanggan' => $pelanggan->nama_pelanggan,
            'url' => 'https://test.gasalamsolusi.my.id/pelanggan/' . $pelanggan->id
        ];
        
        if (generateQR($pelanggan, $data, 'pelanggan', $pelanggan->id)) {
            $totalGenerated++;
        } else {
            $totalErrors++;
        }
    }
} catch (Exception $e) {
    echo "❌ Error processing Pelanggan: " . $e->getMessage() . "\n";
}

// Summary
echo "\n";
echo "🎉 QR code generation completed!\n";
echo "📊 Summary:\n";
echo "   - Total generated: {$totalGenerated}\n";
echo "   - Total errors: {$totalErrors}\n";
echo "   - Success rate: " . ($totalGenerated > 0 ? round(($totalGenerated / ($totalGenerated + $totalErrors)) * 100, 2) : 0) . "%\n";
echo "\n";
echo "🔗 QR codes accessible at:\n";
echo "   - https://test.gasalamsolusi.my.id/storage/qr_codes/tabung/\n";
echo "   - https://test.gasalamsolusi.my.id/storage/qr_codes/armada/\n";
echo "   - https://test.gasalamsolusi.my.id/storage/qr_codes/gudang/\n";
echo "   - https://test.gasalamsolusi.my.id/storage/qr_codes/pelanggan/\n";
echo "\n";
echo "✅ Next: Test API endpoints!\n";
echo "🧪 Test login: POST /api/v1/auth/login\n";
echo "🔍 Test scan: POST /api/v1/scan-qr\n";
echo "\n";
?>
