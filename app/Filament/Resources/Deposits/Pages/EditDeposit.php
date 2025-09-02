<?php

namespace App\Filament\Resources\Deposits\Pages;

use App\Filament\Resources\Deposits\DepositResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeposit extends EditRecord
{
    protected static string $resource = DepositResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
