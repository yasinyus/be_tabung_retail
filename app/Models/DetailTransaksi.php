<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';
    
    protected $fillable = [
        'trx_id',
        'tabung',
    ];
    
    protected $casts = [
        'tabung' => 'array',
    ];
    
    // Relationships
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'trx_id', 'trx_id');
    }
    
    // Accessor untuk mendapatkan daftar kode tabung
    public function getKodeTabungListAttribute()
    {
        if (!$this->tabung || !is_array($this->tabung)) {
            return [];
        }
        
        return collect($this->tabung)->pluck('kode_tabung')->toArray();
    }
    
    // Accessor untuk mendapatkan daftar volume
    public function getVolumeListAttribute()
    {
        if (!$this->tabung || !is_array($this->tabung)) {
            return [];
        }
        
        return collect($this->tabung)->pluck('volume')->toArray();
    }
    
    // Accessor untuk mendapatkan string kode tabung
    public function getKodeTabungStringAttribute()
    {
        return implode(', ', $this->kode_tabung_list);
    }
    
    // Accessor untuk mendapatkan string volume
    public function getVolumeStringAttribute()
    {
        return implode(', ', $this->volume_list);
    }
}
