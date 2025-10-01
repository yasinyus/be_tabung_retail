<?php

namespace App\Filament\Resources\RefundTabungs\Pages;

use App\Filament\Resources\RefundTabungs\RefundTabungResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRefundTabung extends CreateRecord
{
    protected static string $resource = RefundTabungResource::class;

    public function getTitle(): string
    {
        return 'Tambah Refund Tabung';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Convert tabung repeater to array format
        if (isset($data['tabung']) && is_array($data['tabung'])) {
            $tabungCodes = [];
            foreach ($data['tabung'] as $item) {
                if (isset($item['kode_tabung']) && !empty($item['kode_tabung'])) {
                    $tabungCodes[] = $item['kode_tabung'];
                }
            }
            $data['tabung'] = $tabungCodes;
        }

        return $data;
    }
}