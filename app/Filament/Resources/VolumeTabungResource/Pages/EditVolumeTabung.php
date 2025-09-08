<?php

namespace App\Filament\Resources\VolumeTabungResource\Pages;

use App\Filament\Resources\VolumeTabungResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVolumeTabung extends EditRecord
{
    protected static string $resource = VolumeTabungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
