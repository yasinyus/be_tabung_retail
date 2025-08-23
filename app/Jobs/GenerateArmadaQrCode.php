<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Armada;

class GenerateArmadaQrCode implements ShouldQueue
{
    use Queueable;

    public $armada;

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
        // Generate QR code dan update database
        $this->armada->qr_code = base64_encode($this->armada->generateQrCode());
        $this->armada->saveQuietly();
    }
}
