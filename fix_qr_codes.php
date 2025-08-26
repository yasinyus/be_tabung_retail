<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING QR CODES FOR ALL MODELS ===\n";

try {
    // Fix Tabung QR Codes
    echo "1. Fixing Tabung QR Codes...\n";
    $tabungs = \App\Models\Tabung::all();
    foreach ($tabungs as $tabung) {
        try {
            $qrCode = $tabung->generateQrCode();
            $tabung->update(['qr_code' => base64_encode($qrCode)]);
            echo "  ✅ Fixed Tabung: {$tabung->kode_tabung}\n";
        } catch (Exception $e) {
            echo "  ❌ Error Tabung {$tabung->kode_tabung}: {$e->getMessage()}\n";
        }
    }

    // Fix Armada QR Codes
    echo "2. Fixing Armada QR Codes...\n";
    $armadas = \App\Models\Armada::all();
    foreach ($armadas as $armada) {
        try {
            $qrCode = $armada->generateQrCode();
            $armada->update(['qr_code' => base64_encode($qrCode)]);
            echo "  ✅ Fixed Armada: {$armada->nopol}\n";
        } catch (Exception $e) {
            echo "  ❌ Error Armada {$armada->nopol}: {$e->getMessage()}\n";
        }
    }

    // Fix Pelanggan QR Codes
    echo "3. Fixing Pelanggan QR Codes...\n";
    $pelanggans = \App\Models\Pelanggan::all();
    foreach ($pelanggans as $pelanggan) {
        try {
            $qrCode = $pelanggan->generateQrCodeSvg();
            $pelanggan->update(['qr_code' => 'qr_codes/pelanggan_' . $pelanggan->id . '.svg']);
            
            // Save to storage
            $qrPath = storage_path('app/public/qr_codes/pelanggan_' . $pelanggan->id . '.svg');
            @mkdir(dirname($qrPath), 0755, true);
            file_put_contents($qrPath, $qrCode);
            
            echo "  ✅ Fixed Pelanggan: {$pelanggan->kode_pelanggan}\n";
        } catch (Exception $e) {
            echo "  ❌ Error Pelanggan {$pelanggan->kode_pelanggan}: {$e->getMessage()}\n";
        }
    }

    // Fix Gudang QR Codes
    echo "4. Fixing Gudang QR Codes...\n";
    $gudangs = \App\Models\Gudang::all();
    foreach ($gudangs as $gudang) {
        try {
            $qrCode = $gudang->generateQrCodeSvg();
            $gudang->update(['qr_code' => 'qr_codes/gudang_' . $gudang->id . '.svg']);
            
            // Save to storage
            $qrPath = storage_path('app/public/qr_codes/gudang_' . $gudang->id . '.svg');
            @mkdir(dirname($qrPath), 0755, true);
            file_put_contents($qrPath, $qrCode);
            
            echo "  ✅ Fixed Gudang: {$gudang->kode_gudang}\n";
        } catch (Exception $e) {
            echo "  ❌ Error Gudang {$gudang->kode_gudang}: {$e->getMessage()}\n";
        }
    }

    echo "\n=== QR CODE FIX COMPLETED ===\n";
    echo "✅ All QR codes have been regenerated!\n";
    echo "🔄 QR Code modals should now display properly.\n";

} catch (Exception $e) {
    echo "❌ Fatal error: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
}
?>
