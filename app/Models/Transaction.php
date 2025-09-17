<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'trx_id',
        'user_id',
        'customer_id',
        'transaction_date',
        'type',
        'total',
        'harga',
        'jumlah_tabung',
        'payment_method',
        'status',
        'notes'
    ];
    
    protected $casts = [
        'transaction_date' => 'datetime',
        'total' => 'decimal:2',
        'type' => 'string',
        'payment_method' => 'string',
        'status' => 'string',
    ];
    
    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->trx_id)) {
                $transaction->trx_id = 'TRX-' . strtoupper(uniqid());
            }
            if (empty($transaction->transaction_date)) {
                $transaction->transaction_date = now();
            }
        });
    }
    
    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'customer_id');
    }
    
    public function detailTransaksi()
    {
        return $this->hasOne(DetailTransaksi::class, 'trx_id', 'trx_id');
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    
    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }
    
    public function scopePurchases($query)
    {
        return $query->where('type', 'purchase');
    }
    
    // Accessors & Mutators
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
    
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => ['color' => 'warning', 'label' => 'Pending'],
            'paid' => ['color' => 'success', 'label' => 'Paid'],
            'cancelled' => ['color' => 'danger', 'label' => 'Cancelled'],
            'refunded' => ['color' => 'info', 'label' => 'Refunded'],
            default => ['color' => 'gray', 'label' => 'Unknown']
        };
    }
    
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'sale' => ['color' => 'success', 'label' => 'Sale'],
            'purchase' => ['color' => 'info', 'label' => 'Purchase'],
            'refund' => ['color' => 'warning', 'label' => 'Refund'],
            default => ['color' => 'gray', 'label' => 'Unknown']
        };
    }
}
