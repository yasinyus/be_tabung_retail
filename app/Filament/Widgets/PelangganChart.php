<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pelanggan;

class PelangganChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Pelanggan';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $pelangganAgen = Pelanggan::where('jenis_pelanggan', 'agen')->count();
        $pelangganUmum = Pelanggan::where('jenis_pelanggan', 'umum')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pelanggan',
                    'data' => [$pelangganAgen, $pelangganUmum],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // Green for Agen
                        'rgba(59, 130, 246, 0.8)',  // Blue for Umum
                    ],
                    'borderColor' => [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Agen', 'Umum'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
