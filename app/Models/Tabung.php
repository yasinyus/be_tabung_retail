<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Jobs\GenerateTabungQrCode;

class Tabung extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_tabung',
        'seri_tabung',
        'tahun',
        'keterangan',
        'qr_code',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    // Accessor untuk menampilkan informasi lengkap tabung
    public function getFullInfoAttribute(): string
    {
        return "{$this->kode_tabung} - {$this->seri_tabung} ({$this->tahun})";
    }

    // Generate QR Code berdasarkan ID tabung
    public function generateQrCode(): string
    {
        // Buat data QR yang lebih sederhana untuk performa lebih baik
        $qrData = json_encode([
            'id' => $this->id,
            'code' => $this->kode_tabung,
            'url' => url("/tabung/{$this->id}")
        ]);

        return QrCode::size(200) // Kurangi size dari 300 ke 200
            ->margin(1) // Kurangi margin dari 2 ke 1
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
            GenerateTabungQrCode::dispatch($this);
            
            return $qrCode;
        }

        return $this->qr_code;
    }

    // Event untuk auto-generate QR code setelah create/update
    protected static function boot()
    {
        parent::boot();

        static::created(function ($tabung) {
            // Dispatch job untuk generate QR code secara asynchronous
            GenerateTabungQrCode::dispatch($tabung);
        });

        static::updated(function ($tabung) {
            // Generate QR code hanya jika field yang mempengaruhi QR code berubah
            if ($tabung->wasChanged(['kode_tabung', 'seri_tabung', 'tahun'])) {
                // Dispatch job untuk generate QR code secara asynchronous
                GenerateTabungQrCode::dispatch($tabung);
            }
        });
    }
}
