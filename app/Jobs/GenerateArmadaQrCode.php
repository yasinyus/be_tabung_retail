<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use App\Models\Armada;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateArmadaQrCode implements ShouldQueue
{
    use Queueable;

    protected $armada;

    /**
     * Create a new job instance.
     */
    public function __construct(Armada $armada)
    {
        $this->armada = $armada;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate QR data
        $qrData = json_encode([
            'type' => 'armada',
            'id' => $this->armada->id,
            'nopol' => $this->armada->nopol,
            'kapasitas' => $this->armada->kapasitas,
            'url' => url('armada/' . $this->armada->id)
        ]);

        // Generate QR code as SVG (no imagick dependency)
        $qrCode = QrCode::size(200)
            ->margin(1)
            ->format('svg')
            ->generate($qrData);

        // Create directory if not exists
        $directory = 'qr_codes/armada';
        Storage::disk('public')->makeDirectory($directory);

        // Save QR code
        $filename = 'armada_' . $this->armada->id . '.svg';
        $path = $directory . '/' . $filename;
        
        Storage::disk('public')->put($path, $qrCode);

        // Update model
        $this->armada->update(['qr_code' => $path]);
    }
}
