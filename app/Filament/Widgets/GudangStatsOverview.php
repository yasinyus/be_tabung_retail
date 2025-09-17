<?php

namespace App\Filament\Widgets;

use App\Models\Gudang;
use App\Models\StokTabung;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class GudangStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        // Get overall statistics
        $totalTabung = StokTabung::count();
        $totalIsi = StokTabung::where('status', 'Isi')->count();
        $totalKosong = StokTabung::where('status', 'Kosong')->count();
        $totalVolume = StokTabung::where('status', 'Isi')->sum('volume');
        $totalGudang = Gudang::count();
        
        $persentaseIsi = $totalTabung > 0 ? round(($totalIsi / $totalTabung) * 100, 1) : 0;

        return [
            Stat::make('Total Tabung', number_format($totalTabung))
                ->description('Semua tabung gas')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('primary'),
                
            Stat::make('Tabung Isi', number_format($totalIsi))
                ->description($persentaseIsi . '% dari total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Tabung Kosong', number_format($totalKosong))
                ->description((100 - $persentaseIsi) . '% dari total')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
                
            Stat::make('Total Volume', number_format($totalVolume, 1) . ' L')
                ->description('Volume gas tersedia')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info'),
                
            Stat::make('Gudang Aktif', number_format($totalGudang))
                ->description('Lokasi penyimpanan')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('gray'),
        ];
    }
}
