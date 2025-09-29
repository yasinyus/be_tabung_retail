<?php

namespace App\Filament\Resources\HistoryPengisian\Pages;

use App\Filament\Resources\HistoryPengisian\HistoryPengisianResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHistoryPengisian extends CreateRecord
{
    protected static string $resource = HistoryPengisianResource::class;

    public function getTitle(): string
    {
        return 'Tambah History Pengisian';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan tanggal diset jika belum ada
        if (!isset($data['tanggal'])) {
            $data['tanggal'] = now();
        }

        return $data;
    }
}