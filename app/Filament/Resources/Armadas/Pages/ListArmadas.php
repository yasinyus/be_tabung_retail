<?php

namespace App\Filament\Resources\Armadas\Pages;

use App\Filament\Resources\Armadas\ArmadaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArmadas extends ListRecords
{
    protected static string $resource = ArmadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
