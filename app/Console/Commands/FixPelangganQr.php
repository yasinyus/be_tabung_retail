<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Jobs\GeneratePelangganQrCode;

class FixPelangganQr extends Command
{
    protected $signature = 'pelanggan:fix-qr';
    protected $description = 'Fix QR code generation for all pelanggans (synchronous)';

    public function handle()
    {
        $pelanggans = Pelanggan::all();
        
        $this->info("Fixing QR codes for {$pelanggans->count()} pelanggans...");
        
        foreach ($pelanggans as $pelanggan) {
            $this->line("Processing Pelanggan: {$pelanggan->kode_pelanggan}");
            
            // Run job synchronously
            $job = new GeneratePelangganQrCode($pelanggan);
            $job->handle();
            
            // Refresh model
            $pelanggan->refresh();
            
            $status = $pelanggan->qr_code ? '✅' : '❌';
            $this->line("  Result: {$status} " . ($pelanggan->qr_code ?? 'FAILED'));
        }
        
        $this->info("All pelanggans processed!");
        
        return 0;
    }
}
