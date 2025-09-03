<?php

namespace App\Filament\Resources\TabungActivityResource\Pages;

use App\Filament\Resources\TabungActivityResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTabungActivity extends CreateRecord
{
    protected static string $resource = TabungActivityResource::class;

    public function getTitle(): string
    {
        return 'Tambah Aktivitas Tabung';
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set user ID dari auth
        $data['id_user'] = Auth::id();
        
        // Auto-calculate total tabung dari repeater
        if (isset($data['tabung']) && is_array($data['tabung'])) {
            $data['total_tabung'] = count($data['tabung']);
        } else {
            $data['total_tabung'] = 0;
        }
        
        // Convert QR code repeater to array format
        if (isset($data['qr_tabung']) && is_array($data['qr_tabung'])) {
            $qrCodes = [];
            foreach ($data['qr_tabung'] as $item) {
                if (isset($item['qr_code']) && !empty($item['qr_code'])) {
                    $qrCodes[] = $item['qr_code'];
                }
            }
            $data['qr_tabung'] = $qrCodes;
        }
        
        // Generate unique transaction ID if not provided
        if (empty($data['transaksi_id'])) {
            $data['transaksi_id'] = 'TXN-' . date('Ymd') . '-' . sprintf('%03d', rand(1, 999));
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
