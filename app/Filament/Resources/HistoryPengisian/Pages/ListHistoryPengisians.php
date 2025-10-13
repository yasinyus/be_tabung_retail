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
                        // Get current table filters state
                        $tableFilters = $this->table->getFilters();
                        $filters = [];
                        
                        // Extract filter values
                        foreach ($tableFilters as $filterName => $filter) {
                            $state = $filter->getState();
                            if (!empty($state)) {
                                $filters[$filterName] = $state;
                            }
                        }
                        
                        // Generate filename with current date
                        $filename = 'history-pengisian-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
                        
                        // Show success notification
                        Notification::make()
                            ->title('Export Excel berhasil!')
                            ->body('Data history pengisian telah diekspor ke file Excel.')
                            ->success()
                            ->send();
                        
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