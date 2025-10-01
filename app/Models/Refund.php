<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'bast_id',
        'total_refund',
        'status_refund',
    ];

    protected $casts = [
        'total_refund' => 'decimal:2',
    ];

    // Accessor untuk format rupiah
    public function getFormattedTotalRefundAttribute()
    {
        return 'Rp ' . number_format($this->total_refund, 0, ',', '.');
    }
}
