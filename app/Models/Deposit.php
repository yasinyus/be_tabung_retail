<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelanggan_id',
        'kode_pelanggan',
        'nama_pelanggan',
        'saldo',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'tanggal' => 'date',
    ];

    // Relationship dengan Pelanggan
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    // Accessor untuk format saldo
    public function getFormattedSaldoAttribute(): string
    {
        return 'Rp ' . number_format($this->saldo, 0, ',', '.');
    }

    // Boot method untuk auto-fill kode dan nama pelanggan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deposit) {
            if ($deposit->pelanggan_id && !$deposit->kode_pelanggan) {
                $pelanggan = Pelanggan::find($deposit->pelanggan_id);
                if ($pelanggan) {
                    $deposit->kode_pelanggan = $pelanggan->kode_pelanggan;
                    $deposit->nama_pelanggan = $pelanggan->nama_pelanggan;
                }
            }
        });

        static::created(function ($deposit) {
            if ($deposit->kode_pelanggan && $deposit->saldo > 0) {
                $saldo = \App\Models\SaldoPelanggan::firstOrCreate(
                    ['kode_pelanggan' => $deposit->kode_pelanggan],
                    ['saldo' => 0]
                );
                $saldo->saldo += $deposit->saldo;
                $saldo->save();
            }
        });

        static::updating(function ($deposit) {
            if ($deposit->isDirty('pelanggan_id')) {
                $pelanggan = Pelanggan::find($deposit->pelanggan_id);
                if ($pelanggan) {
                    $deposit->kode_pelanggan = $pelanggan->kode_pelanggan;
                    $deposit->nama_pelanggan = $pelanggan->nama_pelanggan;
                }
            }
        });
    }
}
