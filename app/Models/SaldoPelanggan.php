<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoPelanggan extends Model
{
    protected $fillable = [
        'kode_pelanggan',
        'saldo',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kode_pelanggan', 'kode_pelanggan');
    }
}
