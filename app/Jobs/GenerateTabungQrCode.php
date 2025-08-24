<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use App\Models\Tabung;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateTabungQrCode implements ShouldQueue
{
    use Queueable;

    protected $tabung;

    /**
     * Create a new job instance.
     */
    public function __construct(Tabung $tabung)
    {
        $this->tabung = $tabung;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate QR data
        $qrData = json_encode([
            'type' => 'tabung',
            'id' => $this->tabung->id,
            'kode_tabung' => $this->tabung->kode_tabung,
            'seri_tabung' => $this->tabung->seri_tabung,
            'url' => url('tabung/' . $this->tabung->id)
        ]);

        // Generate QR code as SVG (no imagick dependency)
        $qrCode = QrCode::size(200)
            ->margin(1)
            ->format('svg')
            ->generate($qrData);

        // Create directory if not exists
        $directory = 'qr_codes/tabung';
        Storage::disk('public')->makeDirectory($directory);

        // Save QR code
        $filename = 'tabung_' . $this->tabung->id . '.svg';
        $path = $directory . '/' . $filename;
        
        Storage::disk('public')->put($path, $qrCode);

        // Update model
        $this->tabung->update(['qr_code' => $path]);
    }
}
