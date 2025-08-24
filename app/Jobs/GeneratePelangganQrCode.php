<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use App\Models\Pelanggan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GeneratePelangganQrCode implements ShouldQueue
{
    use Queueable;

    protected $pelanggan;

    /**
     * Create a new job instance.
     */
    public function __construct(Pelanggan $pelanggan)
    {
        $this->pelanggan = $pelanggan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate QR data
        $qrData = json_encode([
            'type' => 'pelanggan',
            'id' => $this->pelanggan->id,
            'kode_pelanggan' => $this->pelanggan->kode_pelanggan,
            'nama_pelanggan' => $this->pelanggan->nama_pelanggan,
            'url' => url('pelanggan/' . $this->pelanggan->id)
        ]);

        // Generate QR code as SVG (no imagick dependency)
        $qrCode = QrCode::size(200)
            ->margin(1)
            ->format('svg')
            ->generate($qrData);

        // Create directory if not exists
        $directory = 'qr_codes/pelanggan';
        Storage::disk('public')->makeDirectory($directory);

        // Save QR code
        $filename = 'pelanggan_' . $this->pelanggan->id . '.svg';
        $path = $directory . '/' . $filename;
        
        Storage::disk('public')->put($path, $qrCode);

        // Update model
        $this->pelanggan->update(['qr_code' => $path]);
    }
}
