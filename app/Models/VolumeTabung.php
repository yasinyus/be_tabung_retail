<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VolumeTabung extends Model
{
    use HasFactory;

    protected $table = 'volume_tabungs';

    protected $fillable = [
        'tanggal',
        'lokasi',
        'tabung',
        'nama',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tabung' => 'array', // Cast JSON ke array
    ];

    /**
     * Accessor for lokasi name: map GD/PA/PU/PM prefixes to related names
     */
    public function getNamaLokasiAttribute()
    {
        $lokasi = $this->lokasi;
        if (!$lokasi) return null;

        // Gudang
        if (str_starts_with($lokasi, 'GD')) {
            $gudang = \App\Models\Gudang::where('kode_gudang', $lokasi)->first();
            return $gudang ? $gudang->nama_gudang : $lokasi;
        }

        // Pelanggan (PA, PU, PM)
        if (str_starts_with($lokasi, 'PA') || str_starts_with($lokasi, 'PU') || str_starts_with($lokasi, 'PM')) {
            $pelanggan = \App\Models\Pelanggan::where('kode_pelanggan', $lokasi)->first();
            return $pelanggan ? $pelanggan->nama_pelanggan : $lokasi;
        }

        // Armada nopol or other: return as is
        return $lokasi;
    }
}
