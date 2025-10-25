<?php

namespace App\Filament\Resources\TabungActivityResource\Pages;

use App\Filament\Resources\TabungActivityResource;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTabungActivity extends ViewRecord
{
    protected static string $resource = TabungActivityResource::class;
    
    protected string $view = 'filament.resources.tabung-activity-resource.pages.view-tabung-activity';

    public function getTitle(): string
    {
        return 'Detail Aktivitas Tabung - ' . $this->record->nama_aktivitas;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit')
                ->icon('heroicon-o-pencil'),
            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash'),
        ];
    }
    
    protected function getViewData(): array
    {
        $tabungList = $this->getTabungList();
        $volumeHargaData = $this->getVolumeAndHarga($tabungList);
        
        return [
            'record' => $this->record,
            'tabungList' => $tabungList,
            'totalVolume' => $volumeHargaData['totalVolume'],
            'totalHarga' => $volumeHargaData['totalHarga'],
            'showVolumeHarga' => $this->shouldShowVolumeHarga(),
        ];
    }

    protected function shouldShowVolumeHarga(): bool
    {
        $aktivitasYangMenampilkan = [
            'Kirim Tabung Meter',
            'Kirim Tabung Ke Agen',
            'Kirim Tabung Ke Pelanggan',
        ];
        
        return in_array($this->record->nama_aktivitas, $aktivitasYangMenampilkan);
    }

    protected function getVolumeAndHarga(array $tabungList): array
    {
        $totalVolume = 0;
        $totalHarga = 0;
        
        if (!$this->shouldShowVolumeHarga()) {
            return ['totalVolume' => 0, 'totalHarga' => 0];
        }
        
        foreach ($tabungList as $tabung) {
            $kodeTabung = $tabung['qr_code'];
            
            // Ambil data dari stok_tabung
            $stokTabung = \App\Models\StokTabung::where('kode_tabung', $kodeTabung)->first();
            
            if ($stokTabung) {
                $totalVolume += $stokTabung->volume ?? 0;
                
                // Ambil harga dari tabel pelanggan
                
            }
        }

        $tabungData = \App\Models\Pelanggan::where('kode_pelanggan', $this->record->tujuan)->first();
        if ($tabungData) {
                $totalHarga += $tabungData->harga_tabung ?? 0;
        }
        
        return [
            'totalVolume' => $totalVolume,
            'totalHarga' => $totalHarga * $totalVolume,
        ];
    }

    protected function getTabungList(): array
    {
        if (!$this->record || !$this->record->tabung) {
            return [];
        }

        // Jika tabung adalah array
        if (is_array($this->record->tabung)) {
            return collect($this->record->tabung)->map(function ($tabung, $index) {
                if (is_array($tabung) && isset($tabung['qr_code'])) {
                    return [
                        'no' => $index + 1,
                        'qr_code' => $tabung['qr_code'],
                        'status' => $this->record->status ?? 'Unknown',
                    ];
                }
                
                // Jika hanya string QR code
                if (is_string($tabung)) {
                    return [
                        'no' => $index + 1,
                        'qr_code' => $tabung,
                        'status' => $this->record->status ?? 'Unknown',
                    ];
                }
                
                return null;
            })->filter()->toArray();
        }

        return [];
    }
}
