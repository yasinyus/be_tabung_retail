<?php

namespace App\Filament\Resources\Audits\Pages;

use App\Filament\Resources\Audits\AuditResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAudit extends EditRecord
{
    protected static string $resource = AuditResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle tabung_items conversion
        if (isset($data['tabung_items'])) {
            $tabungData = array_map(function ($item) {
                $status = (isset($item['status']) && $item['status'] === 'Isi') ? 'Isi' : 'Kosong';
                $volume = ($status === 'Isi') ? 20 : 0;
                
                return [
                    'qr_code' => $item['qr_code'] ?? '',
                    'status' => $status,
                    'volume' => $volume,
                ];
            }, $data['tabung_items']);
            
            $data['tabung'] = json_encode($tabungData);
            unset($data['tabung_items']);
        }

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Handle tabung_items conversion
        if (isset($data['tabung_items'])) {
            $tabungData = array_map(function ($item) {
                $status = (isset($item['status']) && $item['status'] === 'Isi') ? 'Isi' : 'Kosong';
                $volume = ($status === 'Isi') ? 20 : 0;
                
                return [
                    'qr_code' => $item['qr_code'] ?? '',
                    'status' => $status,
                    'volume' => $volume,
                ];
            }, $data['tabung_items']);
            
            $data['tabung'] = json_encode($tabungData);
            unset($data['tabung_items']);
        }

        // Update fields individually to avoid mass assignment issues
        if (isset($data['tanggal'])) {
            $record->tanggal = $data['tanggal'];
        }
        if (isset($data['lokasi'])) {
            $record->lokasi = $data['lokasi'];
        }
        if (isset($data['tabung'])) {
            $record->tabung = $data['tabung'];
        }
        if (isset($data['nama'])) {
            $record->nama = $data['nama'];
        }
        if (isset($data['keterangan'])) {
            $record->keterangan = $data['keterangan'];
        }
        
        $record->save();
        
        return $record;
    }
}
