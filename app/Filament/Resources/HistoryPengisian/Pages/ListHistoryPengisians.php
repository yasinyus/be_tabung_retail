<?php

namespace App\Filament\Resources\HistoryPengisian\Pages;

use App\Filament\Resources\HistoryPengisian\HistoryPengisianResource;
use Filament\Resources\Pages\ListRecords;

class ListHistoryPengisians extends ListRecords
{
    protected static string $resource = HistoryPengisianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Removed CreateAction - no create functionality needed for History Pengisian
        ];
    }

    public function getTitle(): string
    {
        return 'History Pengisian Tabung';
    }
}