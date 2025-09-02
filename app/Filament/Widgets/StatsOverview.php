<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Tabung;
use App\Models\Armada;
use App\Models\Pelanggan;
use App\Models\Gudang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Total pengguna sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('Total Tabung', $this->getTabungCount())
                ->description('Total tabung gas')
                ->descriptionIcon('heroicon-m-fire')
                ->color('info'),
                
            Stat::make('Total Armada', $this->getArmadaCount())
                ->description('Total kendaraan')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),
                
            Stat::make('Total Pelanggan', $this->getPelangganCount())
                ->description('Total pelanggan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
                
            Stat::make('Pelanggan Agen', $this->getPelangganAgenCount())
                ->description('Pelanggan jenis agen')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),
                
            Stat::make('Pelanggan Umum', $this->getPelangganUmumCount())
                ->description('Pelanggan jenis umum')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
                
            Stat::make('Total Gudang', $this->getGudangCount())
                ->description('Total gudang')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('gray'),
        ];
    }
    
    private function getTabungCount(): int
    {
        try {
            return Tabung::count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getArmadaCount(): int
    {
        try {
            return Armada::count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getPelangganCount(): int
    {
        try {
            return Pelanggan::count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getPelangganAgenCount(): int
    {
        try {
            return Pelanggan::where('jenis_pelanggan', 'agen')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getPelangganUmumCount(): int
    {
        try {
            return Pelanggan::where('jenis_pelanggan', 'umum')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getGudangCount(): int
    {
        try {
            return Gudang::count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}