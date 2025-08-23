<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Tabung;
use App\Models\Pelanggan;
use App\Models\Armada;
use App\Models\Gudang;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalTabung = Tabung::count();
        $totalPelanggan = Pelanggan::count();
        $totalArmada = Armada::count();
        $totalGudang = Gudang::count();

        // Additional statistics
        $pelangganAgen = Pelanggan::where('jenis_pelanggan', 'agen')->count();
        $pelangganUmum = Pelanggan::where('jenis_pelanggan', 'umum')->count();
        $tabungAktif = Tabung::whereNotNull('qr_code')->count();

        return [
            Stat::make('Total Tabung', $totalTabung)
                ->description('Tabung gas terdaftar')
                ->descriptionIcon('heroicon-m-fire')
                ->chart([7, 12, 8, 15, 9, 11, $totalTabung])
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url('/admin/tabungs'),

            Stat::make('Total Pelanggan', $totalPelanggan)
                ->description("{$pelangganAgen} Agen, {$pelangganUmum} Umum")
                ->descriptionIcon('heroicon-m-users')
                ->chart([3, 7, 5, 8, 6, 9, $totalPelanggan])
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url('/admin/pelanggans'),

            Stat::make('Total Armada', $totalArmada)
                ->description('Kendaraan operasional')
                ->descriptionIcon('heroicon-m-truck')
                ->chart([2, 3, 1, 4, 2, 3, $totalArmada])
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url('/admin/armadas'),

            Stat::make('Total Gudang', $totalGudang)
                ->description('Gudang distribusi')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->chart([1, 2, 1, 3, 2, 2, $totalGudang])
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url('/admin/gudangs'),
        ];
    }

    protected function getColumns(): int
    {
        return 2; // 2 columns on desktop, will stack on mobile
    }
}
