<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use App\Jobs\GeneratePelangganQrCode;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'kode_pelanggan',
        'nama_pelanggan',
        'lokasi_pelanggan',
        'harga_tabung',
        'email',
        'password',
        'jenis_pelanggan',
        'penanggung_jawab',
        'qr_code'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'harga_tabung' => 'decimal:2',
        'jenis_pelanggan' => 'string',
    ];

    protected static function booted()
    {
        static::creating(function ($pelanggan) {
            if ($pelanggan->password) {
                $pelanggan->password = Hash::make($pelanggan->password);
            }
        });

        static::updating(function ($pelanggan) {
            if ($pelanggan->isDirty('password') && $pelanggan->password) {
                $pelanggan->password = Hash::make($pelanggan->password);
            }
        });

        static::created(function ($pelanggan) {
            // Generate QR code immediately when created from form
            $qrCode = base64_encode($pelanggan->generateQrCodeSvg());
            $pelanggan->update(['qr_code' => $qrCode]);
        });

        static::updated(function ($pelanggan) {
            // Regenerate QR code only if relevant fields changed
            if ($pelanggan->wasChanged(['kode_pelanggan', 'nama_pelanggan', 'jenis_pelanggan'])) {
                // Generate immediately
                $qrCode = base64_encode($pelanggan->generateQrCodeSvg());
                $pelanggan->update(['qr_code' => $qrCode]);
            }
        });
    }

    public function generateQrCode()
    {
        GeneratePelangganQrCode::dispatch($this);
    }

    public function generateQrCodeSvg(): string
    {
        $qrData = json_encode([
            'id' => $this->id,
            'code' => $this->kode_pelanggan,
            'type' => 'pelanggan',
            'url' => url("/pelanggan/{$this->id}")
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
        GeneratePelangganQrCode::dispatch($this);
        
        return base64_encode($qrCodeSvg);
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code ? asset('storage/' . $this->qr_code) : null;
    }

    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga_tabung, 2, ',', '.');
    }

    public function getJenisPelangganLabelAttribute()
    {
        return ucfirst($this->jenis_pelanggan);
    }
}
