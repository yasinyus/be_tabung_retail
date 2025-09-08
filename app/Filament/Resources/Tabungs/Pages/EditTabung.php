<?php

namespace App\Filament\Resources\Tabungs\Pages;

use App\Filament\Resources\Tabungs\TabungResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTabung extends EditRecord
{
    protected static string $resource = TabungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
