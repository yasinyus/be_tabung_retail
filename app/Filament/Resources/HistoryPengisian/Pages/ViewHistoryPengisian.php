<?php

namespace App\Filament\Resources\HistoryPengisian\Pages;

use App\Filament\Resources\HistoryPengisian\HistoryPengisianResource;
use Filament\Resources\Pages\ViewRecord;

class ViewHistoryPengisian extends ViewRecord
{
    protected static string $resource = HistoryPengisianResource::class;
    
    protected string $view = 'filament.resources.history-pengisian.view';

    public function getTitle(): string
    {
        return 'Lihat History Pengisian';
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-o-pencil'),
            \Filament\Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash'),
        ];
    }
}