<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerahTerimaTabung extends Model
{
    use HasFactory;

    protected $table = 'serah_terima_tabungs';

    protected $fillable = [
        'bast_id',
        'kode_pelanggan',
        'tabung',
        'total_harga',
        'status',
    ];

    protected $casts = [
        'tabung' => 'array', // Cast JSON ke array
        'total_harga' => 'decimal:2',
    ];

    /**
     * Relasi ke Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kode_pelanggan', 'kode_pelanggan');
    }

    /**
     * Accessor untuk mendapatkan nama pelanggan
     */
    public function getNamaPelangganAttribute()
    {
        return $this->pelanggan ? $this->pelanggan->nama_pelanggan : $this->kode_pelanggan;
    }

    /**
     * Accessor untuk mendapatkan jumlah tabung
     */
    public function getJumlahTabungAttribute()
    {
        return is_array($this->tabung) ? count($this->tabung) : 0;
    }
}