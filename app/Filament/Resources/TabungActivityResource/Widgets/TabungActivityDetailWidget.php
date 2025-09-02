<?php

namespace App\Filament\Resources\TabungActivityResource\Widgets;

use App\Models\TabungActivity;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

class TabungActivityDetailWidget extends Widget
{
    protected string $view = 'filament.resources.tabung-activity-resource.widgets.tabung-activity-detail-widget';

    public ?TabungActivity $record = null;

    protected int | string | array $columnSpan = 'full';

    public function mount(?TabungActivity $record): void
    {
        $this->record = $record;
    }

    protected function getViewData(): array
    {
        Log::info('TabungActivityDetailWidget - Record ID: ' . ($this->record ? $this->record->id : 'null'));
        Log::info('TabungActivityDetailWidget - Tabung data: ' . json_encode($this->record?->tabung));
        
        return [
            'record' => $this->record,
            'tabungList' => $this->getTabungList(),
        ];
    }

    protected function getTabungList(): array
    {
        if (!$this->record || !$this->record->tabung) {
            return [];
        }

        // Jika tabung adalah JSON array dengan struktur qr_code
        if (is_array($this->record->tabung)) {
            return collect($this->record->tabung)->map(function ($tabung) {
                if (is_array($tabung) && isset($tabung['qr_code'])) {
                    return [
                        'qr_code' => $tabung['qr_code'],
                        'status' => $this->record->status ?? 'Unknown',
                    ];
                }
                
                // Jika hanya string QR code
                if (is_string($tabung)) {
                    return [
                        'qr_code' => $tabung,
                        'status' => $this->record->status ?? 'Unknown',
                    ];
                }
                
                return null;
            })->filter()->toArray();
        }

        // Jika tabung adalah string JSON
        if (is_string($this->record->tabung)) {
            $decoded = json_decode($this->record->tabung, true);
            if (is_array($decoded)) {
                return collect($decoded)->map(function ($tabung) {
                    if (is_array($tabung) && isset($tabung['qr_code'])) {
                        return [
                            'qr_code' => $tabung['qr_code'],
                            'status' => $this->record->status ?? 'Unknown',
                        ];
                    }
                    
                    if (is_string($tabung)) {
                        return [
                            'qr_code' => $tabung,
                            'status' => $this->record->status ?? 'Unknown',
                        ];
                    }
                    
                    return null;
                })->filter()->toArray();
            }
        }

        return [];
    }
}
