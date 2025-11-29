<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokTabung extends Model
{
    protected $table = 'stok_tabung';
    
    protected $fillable = [
        'kode_tabung',
        'status',
        'volume',
        'lokasi',
        'tanggal_update'
    ];
    
    protected $casts = [
        'tanggal_update' => 'datetime',
        'volume' => 'decimal:2',
    ];
    
    /**
     * Relasi ke model Tabung
     */
    public function tabung(): BelongsTo
    {
        return $this->belongsTo(Tabung::class, 'kode_tabung', 'kode_tabung');
    }
    
    /**
     * Relasi ke gudang jika lokasi berawalan GD
     */
    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'lokasi', 'kode_gudang');
    }
    
    /**
     * Relasi ke pelanggan jika lokasi berawalan PU/PA
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'lokasi', 'kode_pelanggan');
    }
    
    /**
     * Accessor untuk mendapatkan nama lokasi berdasarkan kolom lokasi
     */
    public function getNamaLokasiAttribute()
    {
        $lokasi = $this->lokasi;
        
        // Jika lokasi berawalan GD, ambil dari tabel gudangs
        if (str_starts_with($lokasi, 'GD')) {
            $gudang = Gudang::where('kode_gudang', $lokasi)->first();
            return $gudang ? $gudang->nama_gudang : $this->lokasi;
        }
        
        // Jika lokasi berawalan PU, PA, atau PM, ambil dari tabel pelanggans
        if (str_starts_with($lokasi, 'PU') || str_starts_with($lokasi, 'PA') || str_starts_with($lokasi, 'PM')) {
            $pelanggan = Pelanggan::where('kode_pelanggan', $lokasi)->first();
            return $pelanggan ? $pelanggan->nama_pelanggan : $this->lokasi;
        }
        
        // Jika tidak ada aturan khusus, gunakan lokasi manual
        return $this->lokasi ?? 'Tidak diketahui';
    }
    
    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeKosong($query)
    {
        return $query->where('status', 'Kosong');
    }
    
    public function scopeIsi($query)
    {
        return $query->where('status', 'Isi');
    }
    
    /**
     * Scope untuk filter berdasarkan lokasi
     */
    public function scopeDiLokasi($query, $lokasi)
    {
        return $query->where('lokasi', $lokasi);
    }
    
    /**
     * Scope untuk filter berdasarkan volume
     */
    public function scopeBervolume($query)
    {
        return $query->whereNotNull('volume')->where('volume', '>', 0);
    }
    
    public function scopeTanpaVolume($query)
    {
        return $query->whereNull('volume')->orWhere('volume', 0);
    }
    
    /**
     * Update tanggal otomatis saat model disimpan
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($model) {
            $model->tanggal_update = now();
        });
    }
}
