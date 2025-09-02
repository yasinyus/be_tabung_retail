<?php

namespace App\Filament\Resources\Armadas\Pages;

use App\Filament\Resources\Armadas\ArmadaResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListArmadas extends ListRecords
{
    protected static string $resource = ArmadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('downloadQrCodes')
            //     ->label('Download QR Codes')
            //     ->icon('heroicon-o-qr-code')
            //     ->color('success')
            //     ->action(function () {
            //         return redirect()->route('armada.qr-codes.pdf');
            //     })
            //     ->tooltip('Download semua QR Code Armada dalam format PDF'),
            Action::make('create')
                ->label('Create Armada')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(ArmadaResource::getUrl('create')),
        ];
    }
}
