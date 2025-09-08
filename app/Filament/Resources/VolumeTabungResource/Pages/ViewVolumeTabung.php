<?php

namespace App\Filament\Resources\VolumeTabungResource\Pages;

use App\Filament\Resources\VolumeTabungResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVolumeTabung extends ViewRecord
{
    protected static string $resource = VolumeTabungResource::class;
    
    protected string $view = 'filament.resources.volume-tabung-resource.pages.view-volume-tabung';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
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
        $tabungData = $this->record->tabung ?? [];
        $tabungList = [];
        
        if (is_array($tabungData)) {
            foreach ($tabungData as $index => $tabung) {
                $tabungList[] = [
                    'no' => $index + 1,
                    'qr_code' => $tabung['qr_code'] ?? '',
                    'status' => $tabung['status'] ?? 'kosong',
                ];
            }
        }
        
        return $tabungList;
    }
}
