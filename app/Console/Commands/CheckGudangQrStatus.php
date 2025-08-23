<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gudang;

class CheckGudangQrStatus extends Command
{
    protected $signature = 'gudang:check-qr';
    protected $description = 'Check QR code status for all gudangs';

    public function handle()
    {
        $gudangs = Gudang::select('id', 'kode_gudang', 'qr_code')->get();
        
        $this->info('Gudang QR Code Status:');
        $this->info('==========================================');
        
        foreach ($gudangs as $gudang) {
            $status = $gudang->qr_code ? '✅ YES' : '❌ NO';
            $this->line("{$gudang->kode_gudang}: {$status}");
        }
        
        $withQr = $gudangs->whereNotNull('qr_code')->count();
        $withoutQr = $gudangs->whereNull('qr_code')->count();
        
        $this->info('==========================================');
        $this->info("Total: {$gudangs->count()} | With QR: {$withQr} | Without QR: {$withoutQr}");
        
        return 0;
    }
}
