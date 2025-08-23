<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Tabung;

class GenerateTabungQrCode implements ShouldQueue
{
    use Queueable;

    public $tabung;

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
        // Generate QR code dan update database
        $this->tabung->qr_code = base64_encode($this->tabung->generateQrCode());
        $this->tabung->saveQuietly();
    }
}
