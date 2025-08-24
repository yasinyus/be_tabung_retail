<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tabung;
use App\Models\Armada;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Jobs\GenerateTabungQrCode;
use App\Jobs\GenerateArmadaQrCode;
use App\Jobs\GenerateGudangQrCode;
use App\Jobs\GeneratePelangganQrCode;
use Illuminate\Support\Facades\Storage;

class FixAllQrCodes extends Command
{
    protected $signature = 'qr:fix-all {--force : Force regenerate all QR codes}';
    protected $description = 'Fix all QR codes for all models (tabung, armada, gudang, pelanggan)';

    public function handle()
    {
        $force = $this->option('force');
        
        $this->info('🔧 Starting QR code fix for all models...');
        
        // Create QR directories
        $this->info('📁 Creating QR code directories...');
        Storage::disk('public')->makeDirectory('qr_codes/tabung');
        Storage::disk('public')->makeDirectory('qr_codes/armada');
        Storage::disk('public')->makeDirectory('qr_codes/gudang');
        Storage::disk('public')->makeDirectory('qr_codes/pelanggan');
        
        // Fix Tabung QR codes
        $this->info('🏷️  Processing Tabung QR codes...');
        $tabungs = Tabung::when(!$force, function ($query) {
            return $query->whereNull('qr_code');
        })->get();
        
        $this->withProgressBar($tabungs, function ($tabung) {
            $job = new GenerateTabungQrCode($tabung);
            $job->handle();
        });
        $this->newLine();
        $this->info("✅ {$tabungs->count()} Tabung QR codes processed");
        
        // Fix Armada QR codes
        $this->info('🚛 Processing Armada QR codes...');
        $armadas = Armada::when(!$force, function ($query) {
            return $query->whereNull('qr_code');
        })->get();
        
        $this->withProgressBar($armadas, function ($armada) {
            $job = new GenerateArmadaQrCode($armada);
            $job->handle();
        });
        $this->newLine();
        $this->info("✅ {$armadas->count()} Armada QR codes processed");
        
        // Fix Gudang QR codes
        $this->info('🏢 Processing Gudang QR codes...');
        $gudangs = Gudang::when(!$force, function ($query) {
            return $query->whereNull('qr_code');
        })->get();
        
        $this->withProgressBar($gudangs, function ($gudang) {
            $job = new GenerateGudangQrCode($gudang);
            $job->handle();
        });
        $this->newLine();
        $this->info("✅ {$gudangs->count()} Gudang QR codes processed");
        
        // Fix Pelanggan QR codes
        $this->info('👥 Processing Pelanggan QR codes...');
        $pelanggans = Pelanggan::when(!$force, function ($query) {
            return $query->whereNull('qr_code');
        })->get();
        
        $this->withProgressBar($pelanggans, function ($pelanggan) {
            $job = new GeneratePelangganQrCode($pelanggan);
            $job->handle();
        });
        $this->newLine();
        $this->info("✅ {$pelanggans->count()} Pelanggan QR codes processed");
        
        // Summary
        $this->newLine();
        $this->info('📊 QR Code Generation Summary:');
        $this->table(
            ['Model', 'Total', 'With QR Code', 'Missing QR Code'],
            [
                ['Tabung', Tabung::count(), Tabung::whereNotNull('qr_code')->count(), Tabung::whereNull('qr_code')->count()],
                ['Armada', Armada::count(), Armada::whereNotNull('qr_code')->count(), Armada::whereNull('qr_code')->count()],
                ['Gudang', Gudang::count(), Gudang::whereNotNull('qr_code')->count(), Gudang::whereNull('qr_code')->count()],
                ['Pelanggan', Pelanggan::count(), Pelanggan::whereNotNull('qr_code')->count(), Pelanggan::whereNull('qr_code')->count()],
            ]
        );
        
        $this->newLine();
        $this->info('🎉 All QR codes have been processed!');
        $this->info('💡 Make sure to run "php artisan storage:link" if not already done');
        $this->info('🔗 QR codes are accessible at: ' . asset('storage/qr_codes/'));
        
        return 0;
    }
}
