<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Pelanggan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        try {
            // Create QR code content with URL to view the pelanggan
            $qrContent = url("/pelanggan/{$this->pelanggan->id}");
            
            // Generate QR code as SVG
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->generate($qrContent);

            // Create directory if it doesn't exist
            $directory = 'public/qrcodes/pelanggan';
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            // Save QR code to storage
            $filename = "pelanggan_{$this->pelanggan->id}_qr.svg";
            $path = "qrcodes/pelanggan/{$filename}";
            
            Storage::disk('public')->put($path, $qrCode);

            // Update pelanggan record with QR code path (using saveQuietly to prevent infinite loop)
            $this->pelanggan->qr_code = $path;
            $this->pelanggan->saveQuietly();

        } catch (\Exception $e) {
            // Log error but don't fail the job
            Log::error("Failed to generate QR code for Pelanggan {$this->pelanggan->id}: " . $e->getMessage());
        }
    }
}
