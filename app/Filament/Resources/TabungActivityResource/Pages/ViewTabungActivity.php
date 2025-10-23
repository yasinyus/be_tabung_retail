<?php

namespace App\Filament\Resources\TabungActivityResource\Pages;

use App\Filament\Resources\TabungActivityResource;
use Filament\Actions\EditAction;
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
        
        // Ambil harga per m³ dari pelanggan berdasarkan tujuan (kode_pelanggan)
        $hargaPerM3 = 0;
        if ($this->record->tujuan) {
            $pelanggan = \App\Models\Pelanggan::where('kode_pelanggan', $this->record->tujuan)
                ->orWhere('nama_pelanggan', $this->record->tujuan)
                ->first();
            
            if ($pelanggan && $pelanggan->harga_tabung) {
                $hargaPerM3 = $pelanggan->harga_tabung;
            }
        }
        
        // Hitung total volume dari semua tabung
        foreach ($tabungList as $tabung) {
            $kodeTabung = $tabung['qr_code'];
            
            // Ambil data dari stok_tabung untuk volume
            $stokTabung = \App\Models\StokTabung::where('kode_tabung', $kodeTabung)->first();
            
            if ($stokTabung) {
                $totalVolume += $stokTabung->volume ?? 0;
            }
        }
        
        // Total Harga = Harga per m³ × Total Volume
        $totalHarga = $hargaPerM3 * $totalVolume;
        
        return [
            'totalVolume' => $totalVolume,
            'totalHarga' => $totalHarga,
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
