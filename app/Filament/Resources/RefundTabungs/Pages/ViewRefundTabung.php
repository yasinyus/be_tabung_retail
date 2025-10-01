<?php

namespace App\Filament\Resources\RefundTabungs\Pages;

use App\Filament\Resources\RefundTabungs\RefundTabungResource;
use App\Filament\Resources\RefundTabungs\Schemas\RefundTabungViewForm;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewRefundTabung extends ViewRecord
{
    protected static string $resource = RefundTabungResource::class;

    public function getTitle(): string
    {
        return 'Lihat Refund Tabung';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit'),
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert tabung array to repeater format for view
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

    public function isReadOnly(): bool
    {
        return true;
    }
}