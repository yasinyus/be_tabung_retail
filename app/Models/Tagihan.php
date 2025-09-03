<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    protected $table = 'pelanggans'; // Base table
    
    protected $fillable = [
        'kode_pelanggan',
        'nama_pelanggan',
    ];
    
    // Relationship to get saldo
    public function saldoPelanggan()
    {
        return $this->hasOne(SaldoPelanggan::class, 'kode_pelanggan', 'kode_pelanggan');
    }
    
    // Accessor for saldo
    public function getSaldoAttribute()
    {
        return $this->saldoPelanggan ? $this->saldoPelanggan->saldo : 0;
    }
    
    // Custom query scope to join with saldo_pelanggan
    public function scopeWithSaldo($query)
    {
        return $query->leftJoin('saldo_pelanggan', 'pelanggans.kode_pelanggan', '=', 'saldo_pelanggan.kode_pelanggan')
                    ->select('pelanggans.*', 'saldo_pelanggan.saldo');
    }
}
