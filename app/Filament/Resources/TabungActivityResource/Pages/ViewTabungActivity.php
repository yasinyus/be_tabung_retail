<?php

namespace App\Filament\Resources\TabungActivityResource\Pages;

use App\Filament\Resources\TabungActivityResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTabungActivity extends ViewRecord
{
    protected static string $resource = TabungActivityResource::class;
    
    protected string $view = 'filament.resources.tabung-activity-resource.pages.view-tabung-activity';

    public function getTitle(): string
    {
        return 'Detail Aktivitas Tabung - ' . $this->record->nama_aktivitas;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit')
                ->icon('heroicon-o-pencil'),
            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash'),
        ];
    }
    
    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
            'tabungList' => $this->getTabungList(),
        ];
    }

    protected function getTabungList(): array
    {
        if (!$this->record || !$this->record->tabung) {
            return [];
        }

        // Jika tabung adalah array
        if (is_array($this->record->tabung)) {
            return collect($this->record->tabung)->map(function ($tabung, $index) {
                if (is_array($tabung) && isset($tabung['qr_code'])) {
                    return [
                        'no' => $index + 1,
                        'qr_code' => $tabung['qr_code'],
                        'status' => $this->record->status ?? 'Unknown',
                    ];
                }
                
                // Jika hanya string QR code
                if (is_string($tabung)) {
                    return [
                        'no' => $index + 1,
                        'qr_code' => $tabung,
                        'status' => $this->record->status ?? 'Unknown',
                    ];
                }
                
                return null;
            })->filter()->toArray();
        }

        return [];
    }
}
