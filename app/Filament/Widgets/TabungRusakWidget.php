<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\StokTabung;

class TabungRusakWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $tabungRusakCount = StokTabung::where('status', 'Rusak')->count();
        
        return [
            Stat::make('Tabung Rusak', $tabungRusakCount)
                ->description('Total tabung dengan status rusak')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->chart([7, 2, 10, 3, 15, 4, 17])
        ];
    }
}
