<?php

namespace App\Filament\Resources\RefundTabungs\Pages;

use App\Filament\Resources\RefundTabungs\RefundTabungResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRefundTabungs extends ListRecords
{
    protected static string $resource = RefundTabungResource::class;

    public function getTitle(): string
    {
        return 'Refund Tabung';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Refund'),
        ];
    }
}