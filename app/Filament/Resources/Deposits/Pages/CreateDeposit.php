<?php

namespace App\Filament\Resources\Deposits\Pages;

use App\Filament\Resources\Deposits\DepositResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Pelanggan;

class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika ada parameter kode_pelanggan dari URL, gunakan itu
        $kode_pelanggan = request()->get('kode_pelanggan');
        
        if ($kode_pelanggan) {
            // Cari pelanggan berdasarkan kode_pelanggan
            $pelanggan = Pelanggan::where('kode_pelanggan', $kode_pelanggan)->first();
            
            if ($pelanggan) {
                $data['kode_pelanggan'] = $pelanggan->kode_pelanggan;
                $data['nama_pelanggan'] = $pelanggan->nama_pelanggan;
            }
        }
        
        return $data;
    }
    
    protected function getFormData(): array
    {
        $data = parent::getFormData();
        
        // Pre-fill form jika ada parameter dari URL
        $kode_pelanggan = request()->get('kode_pelanggan');
        
        if ($kode_pelanggan) {
            $pelanggan = Pelanggan::where('kode_pelanggan', $kode_pelanggan)->first();
            
            if ($pelanggan) {
                $data['kode_pelanggan'] = $pelanggan->kode_pelanggan;
                $data['nama_pelanggan'] = $pelanggan->nama_pelanggan;
            }
        }
        
        return $data;
    }
}
