<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use App\Models\Gudang;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateGudangQrCode implements ShouldQueue
{
    use Queueable;

    protected $gudang;

    /**
     * Create a new job instance.
     */
    public function __construct(Gudang $gudang)
    {
        $this->gudang = $gudang;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate QR data
        $qrData = json_encode([
            'type' => 'gudang',
            'id' => $this->gudang->id,
            'kode_gudang' => $this->gudang->kode_gudang,
            'nama_gudang' => $this->gudang->nama_gudang,
            'url' => url('gudang/' . $this->gudang->id)
        ]);

        // Generate QR code as SVG (no imagick dependency)
        $qrCode = QrCode::size(200)
            ->margin(1)
            ->format('svg')
            ->generate($qrData);

        // Create directory if not exists
        $directory = 'qr_codes/gudang';
        Storage::disk('public')->makeDirectory($directory);

        // Save QR code
        $filename = 'gudang_' . $this->gudang->id . '.svg';
        $path = $directory . '/' . $filename;
        
        Storage::disk('public')->put($path, $qrCode);

        // Update model
        $this->gudang->update(['qr_code' => $path]);
    }
}
