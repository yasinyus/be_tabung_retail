<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Gudang;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        try {
            // Create QR code content with URL to view the gudang
            $qrContent = url("/gudang/{$this->gudang->id}");
            
            // Generate QR code as SVG
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->generate($qrContent);

            // Create directory if it doesn't exist
            $directory = 'public/qrcodes/gudang';
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            // Save QR code to storage
            $filename = "gudang_{$this->gudang->id}_qr.svg";
            $path = "qrcodes/gudang/{$filename}";
            
            Storage::disk('public')->put($path, $qrCode);

            // Update gudang record with QR code path (using saveQuietly to prevent infinite loop)
            $this->gudang->qr_code = $path;
            $this->gudang->saveQuietly();

        } catch (\Exception $e) {
            // Log error but don't fail the job
            Log::error("Failed to generate QR code for Gudang {$this->gudang->id}: " . $e->getMessage());
        }
    }
}
