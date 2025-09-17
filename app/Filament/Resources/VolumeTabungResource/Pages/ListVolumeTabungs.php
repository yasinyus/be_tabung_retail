<?php

namespace App\Filament\Resources\VolumeTabungResource\Pages;

use App\Filament\Resources\VolumeTabungResource;
use App\Models\Gudang;
use App\Models\StokTabung;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListVolumeTabungs extends ListRecords
{
    protected static string $resource = VolumeTabungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_stats')
                ->label('📊 Lihat Statistik')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalHeading('Statistik Tabung per Gudang')
                ->modalContent(view('filament.components.tabung-stats', [
                    'stats' => $this->getStats(),
                    'gudangStats' => $this->getGudangStats()
                ]))
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
            Actions\CreateAction::make(),
            Actions\Action::make('list_gudang')
                ->label('List Nama Gudang')
                ->icon('heroicon-o-building-storefront')
                ->color('info')
                ->url('/admin/volume-tabungs/list-gudang'),
        ];
    }

    private function getStats(): array
    {
        $totalTabung = StokTabung::count();
        $totalIsi = StokTabung::where('status', 'Isi')->count();
        $totalKosong = StokTabung::where('status', 'Kosong')->count();
        $totalVolume = StokTabung::where('status', 'Isi')->sum('volume');
        $totalGudang = Gudang::count();

        return [
            'totalTabung' => $totalTabung,
            'totalIsi' => $totalIsi,
            'totalKosong' => $totalKosong,
            'totalVolume' => $totalVolume,
            'totalGudang' => $totalGudang,
            'persentaseIsi' => $totalTabung > 0 ? round(($totalIsi / $totalTabung) * 100, 1) : 0,
            'persentaseKosong' => $totalTabung > 0 ? round(($totalKosong / $totalTabung) * 100, 1) : 0,
        ];
    }

    private function getGudangStats()
    {
        return Gudang::select([
            'gudangs.nama_gudang',
            'gudangs.kode_gudang',
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang) as total_tabung'),
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang AND status = "Isi") as tabung_isi'),
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang AND status = "Kosong") as tabung_kosong'),
            DB::raw('(SELECT SUM(volume) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang AND status = "Isi") as total_volume')
        ])
        ->orderBy('gudangs.nama_gudang')
        ->get();
    }
}
