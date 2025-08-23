<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Tabung;

class TabungQrChart extends ChartWidget
{
    protected ?string $heading = 'Status QR Code Tabung';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $withQr = Tabung::whereNotNull('qr_code')->count();
        $withoutQr = Tabung::whereNull('qr_code')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status QR Code',
                    'data' => [$withQr, $withoutQr],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.8)',  // Green for With QR
                        'rgba(239, 68, 68, 0.8)',   // Red for Without QR
                    ],
                    'borderColor' => [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Dengan QR Code', 'Tanpa QR Code'],
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
