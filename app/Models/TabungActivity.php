<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabungActivity extends Model
{
    use HasFactory;

    protected $table = 'tabung_activity';

    protected $fillable = [
        'activity',
        'nama_user',
        'qr_tabung',
        'lokasi_gudang',
        'armada',
        'keterangan',
        'status',
        'user_id',
        'transaksi_id',
        'tanggal_aktivitas'
    ];

    protected $casts = [
        'qr_tabung' => 'array', // Cast JSON ke array
        'tanggal_aktivitas' => 'date'
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk mendapatkan jumlah tabung
     */
    public function getTotalTabungAttribute()
    {
        return is_array($this->qr_tabung) ? count($this->qr_tabung) : 0;
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal_aktivitas', $date);
    }

    /**
     * Scope untuk filter berdasarkan activity
     */
    public function scopeByActivity($query, $activity)
    {
        return $query->where('activity', $activity);
    }
}
