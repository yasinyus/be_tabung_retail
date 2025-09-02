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
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tabung' => 'array', // Cast JSON ke array
    ];
}
