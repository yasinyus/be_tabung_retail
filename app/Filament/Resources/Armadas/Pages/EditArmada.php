<?php

namespace App\Filament\Resources\Armadas\Pages;

use App\Filament\Resources\Armadas\ArmadaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArmada extends EditRecord
{
    protected static string $resource = ArmadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
