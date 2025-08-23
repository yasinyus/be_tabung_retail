<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Tabung;
use App\Models\Pelanggan;
use App\Models\Armada;
use App\Models\Gudang;
use Carbon\Carbon;

class MonthlyRegistrationsChart extends ChartWidget
{
    protected ?string $heading = 'Registrasi Bulanan (6 Bulan Terakhir)';
    protected static ?int $sort = 3;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(function ($monthsAgo) {
            return Carbon::now()->subMonths($monthsAgo);
        });

        $tabungData = [];
        $pelangganData = [];
        $armadaData = [];
        $gudangData = [];
        $labels = [];

        foreach ($months as $month) {
            $labels[] = $month->format('M Y');
            
            $tabungData[] = Tabung::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
                
            $pelangganData[] = Pelanggan::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
                
            $armadaData[] = Armada::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
                
            $gudangData[] = Gudang::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tabung',
                    'data' => $tabungData,
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pelanggan',
                    'data' => $pelangganData,
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Armada',
                    'data' => $armadaData,
                    'borderColor' => 'rgba(245, 158, 11, 1)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Gudang',
                    'data' => $gudangData,
                    'borderColor' => 'rgba(139, 92, 246, 1)',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
