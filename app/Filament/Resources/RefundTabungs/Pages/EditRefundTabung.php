<?php

namespace App\Filament\Resources\RefundTabungs\Pages;

use App\Filament\Resources\RefundTabungs\RefundTabungResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRefundTabung extends EditRecord
{
    protected static string $resource = RefundTabungResource::class;

    public function getTitle(): string
    {
        return 'Edit Refund Tabung';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat'),
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert tabung array to repeater format
        if (isset($data['tabung']) && is_array($data['tabung'])) {
            $tabungItems = [];
            foreach ($data['tabung'] as $tabung) {
                // Handle both old format (string) and new format (array)
                if (is_string($tabung)) {
                    $tabungItems[] = ['kode_tabung' => $tabung];
                } elseif (is_array($tabung) && isset($tabung['kode_tabung'])) {
                    $tabungItems[] = ['kode_tabung' => $tabung['kode_tabung']];
                }
            }
            $data['tabung'] = $tabungItems;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert tabung repeater back to array format
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