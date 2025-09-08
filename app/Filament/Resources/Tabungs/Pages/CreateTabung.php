<?php

namespace App\Filament\Resources\Tabungs\Pages;

use App\Filament\Resources\Tabungs\TabungResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTabung extends CreateRecord
{
    protected static string $resource = TabungResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
