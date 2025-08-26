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
            Action::make('create')
                ->label('Create Armada')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(ArmadaResource::getUrl('create')),
        ];
    }
}
