<?php

namespace App\Filament\Resources\Pelanggans\Pages;

use App\Filament\Resources\Pelanggans\PelangganResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListPelanggans extends ListRecords
{
    protected static string $resource = PelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Pelanggan')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(PelangganResource::getUrl('create')),
            // Action::make('downloadQrCodes')
            //     ->label('Download QR Codes')
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->color('success')
            //     ->url(route('pelanggan.qr-codes.pdf'))
            //     ->openUrlInNewTab(),
        ];
    }
}
