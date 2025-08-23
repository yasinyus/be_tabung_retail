<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gudang;
use App\Jobs\GenerateGudangQrCode;

class TestGudangQr extends Command
{
    protected $signature = 'gudang:test-qr {id}';
    protected $description = 'Test QR code generation for specific gudang';

    public function handle()
    {
        $id = $this->argument('id');
        $gudang = Gudang::find($id);
        
        if (!$gudang) {
            $this->error("Gudang with ID {$id} not found");
            return 1;
        }
        
        $this->info("Testing QR code generation for Gudang: {$gudang->kode_gudang}");
        
        // Run job synchronously for testing
        $job = new GenerateGudangQrCode($gudang);
        $job->handle();
        
        // Refresh model
        $gudang->refresh();
        
        $this->info("QR Code path: " . ($gudang->qr_code ?? 'NULL'));
        
        if ($gudang->qr_code) {
            $filePath = storage_path('app/public/' . $gudang->qr_code);
            $this->info("File exists: " . (file_exists($filePath) ? 'YES' : 'NO'));
            if (file_exists($filePath)) {
                $this->info("File size: " . filesize($filePath) . " bytes");
            }
        }
        
        return 0;
    }
}
