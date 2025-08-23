<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gudang;

class GenerateGudangQrCodes extends Command
{
    protected $signature = 'gudang:generate-qr';
    protected $description = 'Generate QR codes for all existing gudangs';

    public function handle()
    {
        $gudangs = Gudang::all();
        
        foreach ($gudangs as $gudang) {
            $gudang->generateQrCode();
            $this->info("Triggered QR code generation for Gudang: {$gudang->kode_gudang}");
        }
        
        $this->info("Total {$gudangs->count()} gudangs processed.");
        return 0;
    }
}
