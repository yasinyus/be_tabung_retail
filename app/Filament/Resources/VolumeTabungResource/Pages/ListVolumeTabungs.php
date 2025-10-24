<?php

namespace App\Filament\Resources\VolumeTabungResource\Pages;

use App\Filament\Resources\VolumeTabungResource;
use App\Models\Armada;
use App\Models\Gudang;
use App\Models\Pelanggan;
use App\Models\StokTabung;
use Filament\Actions;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VolumeTabungExport;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListVolumeTabungs extends ListRecords
{
    protected static string $resource = VolumeTabungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label('⬇️ Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new VolumeTabungExport, 'volume-tabung.xlsx');
                }),
            Actions\Action::make('view_stats')
                ->label('📊 Lihat Statistik')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalHeading('Statistik Tabung per Gudang')
                ->modalContent(view('filament.components.tabung-stats', [
                    'stats' => $this->getStats(),
                    'gudangStats' => $this->getGudangStats(),
                    'pelangganStats' => $this->getPelangganStats(),
                    'armadaStats' => $this->getArmadaStats()
                ]))
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
        ];
    }

    private function getStats(): array
    {
        $totalTabung = StokTabung::count();
        $totalIsi = StokTabung::where('status', 'Isi')->count();
        $totalKosong = StokTabung::where('status', 'Kosong')->count();
        $totalRusak = StokTabung::where('status', 'Rusak')->count();
        $totalVolume = StokTabung::where('status', 'Isi')->sum('volume');
        $totalGudang = Gudang::count();
        $totalPelanggan = Pelanggan::count();
        $totalArmada = Armada::count();

        return [
            'totalTabung' => $totalTabung,
            'totalIsi' => $totalIsi,
            'totalKosong' => $totalKosong,
            'totalRusak' => $totalRusak,
            'totalVolume' => $totalVolume,
            'totalGudang' => $totalGudang,
            'totalPelanggan' => $totalPelanggan,
            'totalArmada' => $totalArmada,
            'persentaseIsi' => $totalTabung > 0 ? round(($totalIsi / $totalTabung) * 100, 1) : 0,
            'persentaseKosong' => $totalTabung > 0 ? round(($totalKosong / $totalTabung) * 100, 1) : 0,
            'persentaseRusak' => $totalTabung > 0 ? round(($totalRusak / $totalTabung) * 100, 1) : 0,
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
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang AND status = "Rusak") as tabung_rusak'),
            DB::raw('(SELECT SUM(volume) FROM stok_tabung WHERE lokasi = gudangs.kode_gudang AND status = "Isi") as total_volume')
        ])
        ->orderBy('gudangs.nama_gudang')
        ->get();
    }

    private function getPelangganStats()
    {
        return Pelanggan::select([
            'pelanggans.nama_pelanggan',
            'pelanggans.kode_pelanggan',
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = pelanggans.kode_pelanggan) as total_tabung'),
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = pelanggans.kode_pelanggan AND status = "Isi") as tabung_isi'),
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = pelanggans.kode_pelanggan AND status = "Kosong") as tabung_kosong'),
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = pelanggans.kode_pelanggan AND status = "Rusak") as tabung_rusak'),
            DB::raw('(SELECT SUM(volume) FROM stok_tabung WHERE lokasi = pelanggans.kode_pelanggan AND status = "Isi") as total_volume')
        ])
        ->orderBy('pelanggans.nama_pelanggan')
        ->get();
    }

    private function getArmadaStats()
    {
        return Armada::select([
            'armadas.nopol',
            'armadas.kode_kendaraan',
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = armadas.kode_kendaraan) as total_tabung'),
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = armadas.kode_kendaraan AND status = "Isi") as tabung_isi'),
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = armadas.kode_kendaraan AND status = "Kosong") as tabung_kosong'),
            DB::raw('(SELECT COUNT(*) FROM stok_tabung WHERE lokasi = armadas.kode_kendaraan AND status = "Rusak") as tabung_rusak'),
            DB::raw('(SELECT SUM(volume) FROM stok_tabung WHERE lokasi = armadas.kode_kendaraan AND status = "Isi") as total_volume')
        ])
        ->orderBy('armadas.nopol')
        ->get();
    }
}
