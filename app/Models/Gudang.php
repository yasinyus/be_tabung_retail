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
            GenerateGudangQrCode::dispatch($gudang);
        });

        static::updated(function ($gudang) {
            // Regenerate QR code only if relevant fields changed
            if ($gudang->wasChanged(['kode_gudang', 'nama_gudang', 'tahun_gudang'])) {
                GenerateGudangQrCode::dispatch($gudang);
            }
        });
    }

    public function generateQrCode()
    {
        GenerateGudangQrCode::dispatch($this);
    }

    public function getQrCodeBase64()
    {
        if (!$this->qr_code || !file_exists(storage_path('app/public/' . $this->qr_code))) {
            return null;
        }

        $qrCodeContent = file_get_contents(storage_path('app/public/' . $this->qr_code));
        return base64_encode($qrCodeContent);
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? asset('storage/' . $this->qr_code) : null;
    }
}
