<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasTabung extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_tabungs';

    protected $fillable = [
        'nama_aktivitas',
        'dari',
        'tujuan',
        'tabung',
        'keterangan',
        'nama_petugas',
        'id_user',
        'total_tabung',
        'tanggal',
        'waktu',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime:H:i',
        'tabung' => 'array', // Cast JSON ke array
        'total_tabung' => 'integer',
    ];

    /**
     * Relationship dengan User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Accessor untuk mendapatkan total tabung dari array tabung
     */
    public function getTotalTabungAttribute($value)
    {
        if ($this->tabung && is_array($this->tabung)) {
            return count($this->tabung);
        }
        return $value ?? 0;
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }
}
