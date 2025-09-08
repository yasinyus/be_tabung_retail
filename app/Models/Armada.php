<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Jobs\GenerateArmadaQrCode;

class Armada extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kendaraan',
        'nopol',
        'kapasitas',
        'tahun',
        'keterangan',
        'qr_code',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'kapasitas' => 'integer',
    ];

    // Accessor untuk menampilkan informasi lengkap armada
    public function getFullInfoAttribute(): string
    {
        return "{$this->nopol} - {$this->kapasitas} ton ({$this->tahun})";
    }

    // Generate QR Code berdasarkan ID armada
    public function generateQrCode(): string
    {
        // Buat data QR yang lebih sederhana untuk performa lebih baik
        $qrData = json_encode([
            'id' => $this->id,
            'nopol' => $this->nopol,
            'url' => url("/armada/{$this->id}")
        ]);

        return QrCode::size(200)
            ->margin(1)
            ->generate($qrData);
    }

    // Get QR Code as base64 string untuk display
    public function getQrCodeBase64(): string
    {
        if (!$this->qr_code) {
            // Jika QR code belum ada, generate secara sync untuk display langsung
            // Tapi juga dispatch job untuk update database
            $qrCode = base64_encode($this->generateQrCode());
            
            // Dispatch job untuk update database
            GenerateArmadaQrCode::dispatch($this);
            
            return $qrCode;
        }

        return $this->qr_code;
    }

    // Event untuk auto-generate QR code setelah create/update
    protected static function boot()
    {
        parent::boot();

        static::created(function ($armada) {
            // Generate QR code immediately when created from form
            $qrCode = base64_encode($armada->generateQrCode());
            // Use updateQuietly to avoid triggering events
            $armada->updateQuietly(['qr_code' => $qrCode]);
        });

        static::updated(function ($armada) {
            // Generate QR code hanya jika field yang mempengaruhi QR code berubah
            if ($armada->wasChanged(['nopol', 'kapasitas', 'tahun']) && !$armada->wasChanged(['qr_code'])) {
                // Generate immediately, but only if qr_code wasn't already updated
                $qrCode = base64_encode($armada->generateQrCode());
                // Use updateQuietly to avoid triggering events
                $armada->updateQuietly(['qr_code' => $qrCode]);
            }
        });
    }
}
