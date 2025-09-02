<?php

namespace App\Filament\Resources\TabungActivityResource\Pages;

use App\Filament\Resources\TabungActivityResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTabungActivities extends ListRecords
{
    protected static string $resource = TabungActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Tambah Aktivitas')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(TabungActivityResource::getUrl('create')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Bisa ditambahkan widget statistik di sini
        ];
    }

    public function getTitle(): string
    {
        return 'Aktivitas Tabung';
    }
    
    protected function getFooterWidgets(): array
    {
        return [];
    }
}
