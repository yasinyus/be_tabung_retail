<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gudang;
use App\Jobs\GenerateGudangQrCode;

class FixGudangQr extends Command
{
    protected $signature = 'gudang:fix-qr';
    protected $description = 'Fix QR code generation for all gudangs (synchronous)';

    public function handle()
    {
        $gudangs = Gudang::all();
        
        $this->info("Fixing QR codes for {$gudangs->count()} gudangs...");
        
        foreach ($gudangs as $gudang) {
            $this->line("Processing Gudang: {$gudang->kode_gudang}");
            
            // Run job synchronously
            $job = new GenerateGudangQrCode($gudang);
            $job->handle();
            
            // Refresh model
            $gudang->refresh();
            
            $status = $gudang->qr_code ? '✅' : '❌';
            $this->line("  Result: {$status} " . ($gudang->qr_code ?? 'FAILED'));
        }
        
        $this->info("All gudangs processed!");
        
        return 0;
    }
}
