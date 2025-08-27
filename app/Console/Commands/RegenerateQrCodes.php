<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tabung;
use App\Models\Armada;
use App\Models\Pelanggan;
use App\Models\Gudang;

class RegenerateQrCodes extends Command
{
    protected $signature = 'qr:regenerate {--model=all : Specify model: tabung, armada, pelanggan, gudang, or all}';
    
    protected $description = 'Regenerate QR codes for all models or specific model';

    public function handle()
    {
        $model = $this->option('model');
        
        $this->info("🔄 Regenerating QR codes for: {$model}");
        
        switch ($model) {
            case 'tabung':
                $this->regenerateTabung();
                break;
            case 'armada':
                $this->regenerateArmada();
                break;
            case 'pelanggan':
                $this->regeneratePelanggan();
                break;
            case 'gudang':
                $this->regenerateGudang();
                break;
            case 'all':
            default:
                $this->regenerateTabung();
                $this->regenerateArmada();
                $this->regeneratePelanggan();
                $this->regenerateGudang();
                break;
        }
        
        $this->info("✅ QR code regeneration completed!");
    }
    
    private function regenerateTabung()
    {
        $this->info("📦 Regenerating Tabung QR codes...");
        
        $tabungs = Tabung::all();
        $count = 0;
        
        foreach ($tabungs as $tabung) {
            try {
                $qrCode = base64_encode($tabung->generateQrCode());
                $tabung->update(['qr_code' => $qrCode]);
                $count++;
                $this->line("   ✅ {$tabung->kode_tabung}");
            } catch (\Exception $e) {
                $this->error("   ❌ Error for {$tabung->kode_tabung}: {$e->getMessage()}");
            }
        }
        
        $this->info("   📊 Processed {$count} Tabung records");
    }
    
    private function regenerateArmada()
    {
        $this->info("🚛 Regenerating Armada QR codes...");
        
        $armadas = Armada::all();
        $count = 0;
        
        foreach ($armadas as $armada) {
            try {
                $qrCode = base64_encode($armada->generateQrCode());
                $armada->update(['qr_code' => $qrCode]);
                $count++;
                $this->line("   ✅ {$armada->nopol}");
            } catch (\Exception $e) {
                $this->error("   ❌ Error for {$armada->nopol}: {$e->getMessage()}");
            }
        }
        
        $this->info("   📊 Processed {$count} Armada records");
    }
    
    private function regeneratePelanggan()
    {
        $this->info("👥 Regenerating Pelanggan QR codes...");
        
        $pelanggans = Pelanggan::all();
        $count = 0;
        
        foreach ($pelanggans as $pelanggan) {
            try {
                $qrCode = base64_encode($pelanggan->generateQrCodeSvg());
                $pelanggan->update(['qr_code' => $qrCode]);
                $count++;
                $this->line("   ✅ {$pelanggan->kode_pelanggan} - {$pelanggan->nama_pelanggan}");
            } catch (\Exception $e) {
                $this->error("   ❌ Error for {$pelanggan->kode_pelanggan}: {$e->getMessage()}");
            }
        }
        
        $this->info("   📊 Processed {$count} Pelanggan records");
    }
    
    private function regenerateGudang()
    {
        $this->info("🏪 Regenerating Gudang QR codes...");
        
        $gudangs = Gudang::all();
        $count = 0;
        
        foreach ($gudangs as $gudang) {
            try {
                $qrCode = base64_encode($gudang->generateQrCodeSvg());
                $gudang->update(['qr_code' => $qrCode]);
                $count++;
                $this->line("   ✅ {$gudang->kode_gudang} - {$gudang->nama_gudang}");
            } catch (\Exception $e) {
                $this->error("   ❌ Error for {$gudang->kode_gudang}: {$e->getMessage()}");
            }
        }
        
        $this->info("   📊 Processed {$count} Gudang records");
    }
}
