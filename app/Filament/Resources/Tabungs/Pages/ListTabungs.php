<?php

namespace App\Filament\Resources\Tabungs\Pages;

use App\Filament\Resources\Tabungs\TabungResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTabungs extends ListRecords
{
    protected static string $resource = TabungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Tabung')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(TabungResource::getUrl('create')),
            // Action::make('downloadQrCodes')
            //     ->label('Download QR Codes')
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->color('success')
            //     ->url(route('tabung.qr-codes.pdf'))
            //     ->openUrlInNewTab(),
        ];
    }
}
