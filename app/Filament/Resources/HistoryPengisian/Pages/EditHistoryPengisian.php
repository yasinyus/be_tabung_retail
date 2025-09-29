<?php

namespace App\Filament\Resources\HistoryPengisian\Pages;

use App\Filament\Resources\HistoryPengisian\HistoryPengisianResource;
use Filament\Resources\Pages\EditRecord;

class EditHistoryPengisian extends EditRecord
{
    protected static string $resource = HistoryPengisianResource::class;

    public function getTitle(): string
    {
        return 'Edit History Pengisian';
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye'),
            \Filament\Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}