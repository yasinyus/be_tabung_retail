<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabungActivity extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_tabung';

    protected $fillable = [
        'nama_aktivitas',
        'dari',
        'tujuan',
        'tabung',
        'keterangan',
        'nama_petugas',
        'id_user',
        'total_tabung',
        'total_harga',
        'tanggal',
        'status',
        'waktu'
    ];

    protected $casts = [
        'tabung' => 'array', // Cast JSON ke array
        'waktu' => 'datetime',
        'tanggal' => 'string'
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Accessor untuk mapping activity ke nama_aktivitas
     */
    public function getActivityAttribute()
    {
        return $this->nama_aktivitas;
    }

    /**
     * Accessor untuk mendapatkan jumlah tabung
     */
    public function getTotalTabungAttribute()
    {
        return $this->total_tabung ?? (is_array($this->tabung) ? count($this->tabung) : 0);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('id_user', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->where('tanggal', $date);
    }

    /**
     * Scope untuk filter berdasarkan aktivitas
     */
    public function scopeByActivity($query, $activity)
    {
        return $query->where('nama_aktivitas', $activity);
    }
}
