<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\GenerateGudangQrCode;

class Gudang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_gudang',
        'nama_gudang', 
        'tahun_gudang',
        'keterangan',
        'qr_code'
    ];

    protected $casts = [
        'tahun_gudang' => 'integer',
    ];

    protected static function booted()
    {
        static::created(function ($gudang) {
            // Generate QR code immediately when created from form
            $qrCode = base64_encode($gudang->generateQrCodeSvg());
            // Use updateQuietly to avoid triggering events
            $gudang->updateQuietly(['qr_code' => $qrCode]);
        });

        static::updated(function ($gudang) {
            // Regenerate QR code only if relevant fields changed
            if ($gudang->wasChanged(['kode_gudang', 'nama_gudang', 'tahun_gudang']) && !$gudang->wasChanged(['qr_code'])) {
                // Generate immediately, but only if qr_code wasn't already updated
                $qrCode = base64_encode($gudang->generateQrCodeSvg());
                // Use updateQuietly to avoid triggering events
                $gudang->updateQuietly(['qr_code' => $qrCode]);
            }
        });
    }

    public function generateQrCode()
    {
        GenerateGudangQrCode::dispatch($this);
    }

    public function generateQrCodeSvg(): string
    {
        $qrData = json_encode([
            'id' => $this->id,
            'code' => $this->kode_gudang,
            'type' => 'gudang',
            'url' => url("/gudang/{$this->id}")
        ]);

        return \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)
            ->margin(1)
            ->generate($qrData);
    }

    public function getQrCodeBase64()
    {
        // Coba ambil dari file storage dulu
        if ($this->qr_code && file_exists(storage_path('app/public/' . $this->qr_code))) {
            $qrCodeContent = file_get_contents(storage_path('app/public/' . $this->qr_code));
            return base64_encode($qrCodeContent);
        }

        // Jika tidak ada, generate langsung untuk display
        $qrCodeSvg = $this->generateQrCodeSvg();
        
        // Dispatch job untuk update database
        GenerateGudangQrCode::dispatch($this);
        
        return base64_encode($qrCodeSvg);
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? asset('storage/' . $this->qr_code) : null;
    }
}
