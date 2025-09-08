<?php

namespace App\Filament\Resources\Armadas\Pages;

use App\Filament\Resources\Armadas\ArmadaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArmada extends CreateRecord
{
    protected static string $resource = ArmadaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}


