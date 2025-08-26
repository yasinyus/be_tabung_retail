<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:fix {--force : Force regenerate all QR codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix and regenerate QR codes for all models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting QR Code fix process...');

        // Create storage directory if not exists
        $qrDir = storage_path('app/public/qr_codes');
        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0755, true);
            $this->info('📁 Created QR codes directory');
        }

        // Fix Tabung QR Codes
        $this->info('1️⃣  Fixing Tabung QR Codes...');
        $tabungs = \App\Models\Tabung::all();
        $tabungCount = 0;
        foreach ($tabungs as $tabung) {
            try {
                $qrCode = $tabung->generateQrCode();
                $tabung->update(['qr_code' => base64_encode($qrCode)]);
                $tabungCount++;
            } catch (\Exception $e) {
                $this->error("❌ Error Tabung {$tabung->kode_tabung}: {$e->getMessage()}");
            }
        }
        $this->info("✅ Fixed {$tabungCount} Tabung QR codes");

        // Fix Armada QR Codes
        $this->info('2️⃣  Fixing Armada QR Codes...');
        $armadas = \App\Models\Armada::all();
        $armadaCount = 0;
        foreach ($armadas as $armada) {
            try {
                $qrCode = $armada->generateQrCode();
                $armada->update(['qr_code' => base64_encode($qrCode)]);
                $armadaCount++;
            } catch (\Exception $e) {
                $this->error("❌ Error Armada {$armada->nopol}: {$e->getMessage()}");
            }
        }
        $this->info("✅ Fixed {$armadaCount} Armada QR codes");

        // Fix Pelanggan QR Codes
        $this->info('3️⃣  Fixing Pelanggan QR Codes...');
        $pelanggans = \App\Models\Pelanggan::all();
        $pelangganCount = 0;
        foreach ($pelanggans as $pelanggan) {
            try {
                $qrCode = $pelanggan->generateQrCodeSvg();
                $fileName = 'qr_codes/pelanggan_' . $pelanggan->id . '.svg';
                $pelanggan->update(['qr_code' => $fileName]);
                
                // Save to storage
                $qrPath = storage_path('app/public/' . $fileName);
                file_put_contents($qrPath, $qrCode);
                $pelangganCount++;
            } catch (\Exception $e) {
                $this->error("❌ Error Pelanggan {$pelanggan->kode_pelanggan}: {$e->getMessage()}");
            }
        }
        $this->info("✅ Fixed {$pelangganCount} Pelanggan QR codes");

        // Fix Gudang QR Codes
        $this->info('4️⃣  Fixing Gudang QR Codes...');
        $gudangs = \App\Models\Gudang::all();
        $gudangCount = 0;
        foreach ($gudangs as $gudang) {
            try {
                $qrCode = $gudang->generateQrCodeSvg();
                $fileName = 'qr_codes/gudang_' . $gudang->id . '.svg';
                $gudang->update(['qr_code' => $fileName]);
                
                // Save to storage
                $qrPath = storage_path('app/public/' . $fileName);
                file_put_contents($qrPath, $qrCode);
                $gudangCount++;
            } catch (\Exception $e) {
                $this->error("❌ Error Gudang {$gudang->kode_gudang}: {$e->getMessage()}");
            }
        }
        $this->info("✅ Fixed {$gudangCount} Gudang QR codes");

        $total = $tabungCount + $armadaCount + $pelangganCount + $gudangCount;
        $this->info("🎉 QR Code fix completed! Total: {$total} QR codes regenerated");
        $this->info("🔄 QR Code modals should now display properly");
        
        return 0;
    }
}
