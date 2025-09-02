<?php

namespace App\Filament\Resources\TabungActivityResource\Pages;

use App\Filament\Resources\TabungActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTabungActivity extends EditRecord
{
    protected static string $resource = TabungActivityResource::class;

    public function getTitle(): string
    {
        return 'Edit Aktivitas Tabung';
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
        // Convert QR code array back to repeater format
        if (isset($data['qr_tabung']) && is_array($data['qr_tabung'])) {
            $qrItems = [];
            foreach ($data['qr_tabung'] as $qrCode) {
                $qrItems[] = ['qr_code' => $qrCode];
            }
            $data['qr_tabung'] = $qrItems;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert QR code repeater back to array format
        if (isset($data['qr_tabung']) && is_array($data['qr_tabung'])) {
            $qrCodes = [];
            foreach ($data['qr_tabung'] as $item) {
                if (isset($item['qr_code']) && !empty($item['qr_code'])) {
                    $qrCodes[] = $item['qr_code'];
                }
            }
            $data['qr_tabung'] = $qrCodes;
        }

        return $data;
    }
}
