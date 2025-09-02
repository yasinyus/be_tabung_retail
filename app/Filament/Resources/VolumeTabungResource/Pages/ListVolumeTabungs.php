<?php

namespace App\Filament\Resources\VolumeTabungResource\Pages;

use App\Filament\Resources\VolumeTabungResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVolumeTabungs extends ListRecords
{
    protected static string $resource = VolumeTabungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
