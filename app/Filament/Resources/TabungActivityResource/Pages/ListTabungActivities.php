<?php

namespace App\Filament\Resources\TabungActivityResource\Pages;

use App\Filament\Resources\TabungActivityResource;
use App\Exports\TabungActivitiesExport;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTabungActivities extends ListRecords
{
    protected static string $resource = TabungActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Ambil filters yang aktif dari table
                    $filters = $this->tableFilters ?? [];
                    
                    return Excel::download(
                        new TabungActivitiesExport($filters), 
                        'aktivitas-tabung-' . date('Y-m-d-His') . '.xlsx'
                    );
                }),
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
