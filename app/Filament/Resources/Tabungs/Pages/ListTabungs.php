<?php

namespace App\Filament\Resources\Tabungs\Pages;

use App\Filament\Resources\Tabungs\TabungResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTabungs extends ListRecords
{
    protected static string $resource = TabungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
