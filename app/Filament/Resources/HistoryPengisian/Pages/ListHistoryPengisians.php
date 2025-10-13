<?php

namespace App\Filament\Resources\HistoryPengisian\Pages;

use App\Filament\Resources\HistoryPengisian\HistoryPengisianResource;
use App\Exports\HistoryPengisianExport;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListHistoryPengisians extends ListRecords
{
    protected static string $resource = HistoryPengisianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    try {
                        // For now, export all data without filters to ensure it works
                        // We'll add filter support later once basic export is working
                        $filters = [];
                        
                        // Generate filename with current date
                        $filename = 'history-pengisian-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
                        
                        // Create export and download
                        return Excel::download(new HistoryPengisianExport($filters), $filename);
                        
                    } catch (\Exception $e) {
                        // Show error notification
                        Notification::make()
                            ->title('Export Excel gagal!')
                            ->body('Terjadi kesalahan saat mengekspor data: ' . $e->getMessage())
                            ->danger()
                            ->send();
                        
                        return null;
                    }
                })
                ->tooltip('Download data history pengisian dalam format Excel'),
        ];
    }

    public function getTitle(): string
    {
        return 'History Pengisian Tabung';
    }
}