<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPelanggan extends Model
{
    protected $table = 'laporan_pelanggan';
    
    protected $fillable = [
        'tanggal',
        'kode_pelanggan',
        'keterangan',
        'list_tabung',
        'tabung',
        'harga',
        'tambahan_deposit',
        'pengurangan_deposit',
        'sisa_deposit',
        'konfirmasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'list_tabung' => 'json',
        'harga' => 'decimal:2',
        'tambahan_deposit' => 'decimal:2',
        'pengurangan_deposit' => 'decimal:2',
        'sisa_deposit' => 'decimal:2',
        'konfirmasi' => 'boolean',
    ];

    // Relationships
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'kode_pelanggan', 'kode_pelanggan');
    }

    // Accessors
    public function getFormattedHargaAttribute()
    {
        return $this->harga ? 'Rp ' . number_format($this->harga, 0, ',', '.') : '-';
    }

    public function getFormattedTambahanDepositAttribute()
    {
        return $this->tambahan_deposit ? 'Rp ' . number_format($this->tambahan_deposit, 0, ',', '.') : '-';
    }

    public function getFormattedPenguranganDepositAttribute()
    {
        return $this->pengurangan_deposit ? 'Rp ' . number_format($this->pengurangan_deposit, 0, ',', '.') : '-';
    }

    public function getFormattedSisaDepositAttribute()
    {
        return $this->sisa_deposit ? 'Rp ' . number_format($this->sisa_deposit, 0, ',', '.') : '-';
    }

    public function getKonfirmasiLabelAttribute()
    {
        return $this->konfirmasi ? 'Ya' : 'Tidak';
    }

    public function getExportPdfAttribute()
    {
        return 'Download PDF';
    }

    // Scopes
    public function scopeByPelanggan($query, $kodePelanggan)
    {
        return $query->where('kode_pelanggan', $kodePelanggan);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    public function scopeKonfirmasi($query, $status = true)
    {
        return $query->where('konfirmasi', $status);
    }
}
