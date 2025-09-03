<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-populate user_id dari user yang sedang login
        $data['user_id'] = Auth::id();
        
        // Auto-generate TRX ID jika belum ada
        if (empty($data['trx_id'])) {
            $data['trx_id'] = 'TRX-' . strtoupper(uniqid());
        }
        
        return $data;
    }
}
