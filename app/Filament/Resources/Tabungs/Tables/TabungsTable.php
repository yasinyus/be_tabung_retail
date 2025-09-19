<?php

namespace App\Filament\Resources\Tabungs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use App\Filament\Resources\Tabungs\TabungResource;
use App\Models\TabungActivity;
use App\Exports\TabungExport;
use App\Exports\TabungTemplateExport;
use App\Imports\TabungImport;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class TabungsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_tabung')
                    ->label('Kode Tabung')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('seri_tabung')
                    ->label('Seri Tabung')
                    ->searchable()
                    ->sortable(),
                    
                BadgeColumn::make('tahun')
                    ->label('Tahun Produksi')
                    ->colors([
                        'danger' => fn ($state) => $state < date('Y') - 10,
                        'warning' => fn ($state) => $state < date('Y') - 5,
                        'success' => fn ($state) => $state >= date('Y') - 5,
                    ])
                    ->sortable(),
                    
                TextColumn::make('siklus')
                    ->label('Siklus')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tidak ada siklus'),
                    
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->placeholder('Tidak ada keterangan'),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->label('Filter Tahun')
                    ->options(function () {
                        $currentYear = date('Y');
                        $startYear = $currentYear - 20;
                        $endYear = $currentYear + 5;
                        
                        $years = [];
                        for ($year = $endYear; $year >= $startYear; $year--) {
                            $years[$year] = $year;
                        }
                        
                        return $years;
                    }),
                    
                SelectFilter::make('siklus')
                    ->label('Filter Siklus')
                    ->options(function () {
                        // Get unique siklus values from database
                        return \App\Models\Tabung::whereNotNull('siklus')
                            ->distinct()
                            ->pluck('siklus', 'siklus')
                            ->toArray();
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('kode_tabung')
                            ->label('Kode Tabung')
                            ->default(fn ($record) => $record->kode_tabung)
                            ->disabled(),
                            
                        \Filament\Forms\Components\TextInput::make('seri_tabung')
                            ->label('Seri Tabung')
                            ->default(fn ($record) => $record->seri_tabung)
                            ->disabled(),
                            
                        \Filament\Forms\Components\TextInput::make('tahun')
                            ->label('Tahun Produksi')
                            ->default(fn ($record) => $record->tahun)
                            ->disabled(),
                            
                        \Filament\Forms\Components\TextInput::make('siklus')
                            ->label('Siklus')
                            ->default(fn ($record) => $record->siklus)
                            ->disabled(),
                            
                        \Filament\Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->default(fn ($record) => $record->keterangan)
                            ->disabled()
                            ->rows(3),
                            
                        \Filament\Forms\Components\TextInput::make('created_at')
                            ->label('Dibuat Pada')
                            ->default(fn ($record) => $record->created_at?->format('d-m-Y H:i:s'))
                            ->disabled(),
                            
                        \Filament\Forms\Components\TextInput::make('updated_at')
                            ->label('Diupdate Pada')
                            ->default(fn ($record) => $record->updated_at?->format('d-m-Y H:i:s'))
                            ->disabled(),
                            
                        \Filament\Forms\Components\ViewField::make('aktivitas_tabung')
                            ->label('Riwayat Aktivitas Tabung')
                            ->view('filament.components.tabung-activity-table')
                            ->viewData(function ($record) {
                                // Get activities yang memiliki kode tabung ini dalam array JSON
                                $activities = TabungActivity::where(function($query) use ($record) {
                                    $query->whereRaw("JSON_SEARCH(tabung, 'one', ?) IS NOT NULL", [$record->kode_tabung]);
                                })
                                ->orderBy('created_at', 'desc')
                                ->get();
                                
                                return ['activities' => $activities];
                            }),
                    ])
                    ->modalHeading(fn ($record) => 'Detail Tabung - ' . $record->kode_tabung)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('6xl'),
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => TabungResource::getUrl('edit', ['record' => $record]))
                    ->color('warning'),
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn ($record) => "QR Code - {$record->kode_tabung}")
                    ->modalContent(function ($record) {
                        // Check jika QR code sudah ada atau belum
                        if (!$record->qr_code) {
                            return new HtmlString("
                                <div class='text-center p-6'>
                                    <div class='mb-4'>
                                        <svg class='animate-spin h-8 w-8 mx-auto text-blue-600' fill='none' viewBox='0 0 24 24'>
                                            <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                                            <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
                                        </svg>
                                    </div>
                                    <p class='text-gray-600'>QR Code sedang diproses...</p>
                                    <p class='text-sm text-gray-500 mt-2'>Silakan coba lagi dalam beberapa detik</p>
                                </div>
                            ");
                        }
                        
                        $qrCodeBase64 = $record->getQrCodeBase64();
                        
                        return new HtmlString("
                            <div class='text-center p-6'>
                                <img src='data:image/svg+xml;base64,{$qrCodeBase64}' 
                                     alt='QR Code for {$record->kode_tabung}' 
                                     class='mx-auto border rounded-lg shadow-lg'
                                     style='max-width: 300px; width: 100%;'>
                                <div class='mt-4 text-sm text-gray-600'>
                                    <p><strong>Kode Tabung:</strong> {$record->kode_tabung}</p>
                                    <p><strong>Seri Tabung:</strong> {$record->seri_tabung}</p>
                                    <p><strong>Tahun:</strong> {$record->tahun}</p>
                                    " . ($record->siklus ? "<p><strong>Siklus:</strong> {$record->siklus}</p>" : "") . "
                                </div>
                                <div class='mt-4 text-xs text-gray-500'>
                                    Scan QR Code ini untuk melihat detail tabung
                                </div>
                            </div>
                        ");
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->slideOver(),
            ])
            ->toolbarActions([
                Action::make('download_template')
                    ->label('Download Template')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function () {
                        return Excel::download(new TabungTemplateExport, 'template-tabung.xlsx');
                    }),
                    
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new TabungExport, 'tabung-' . date('Y-m-d') . '.xlsx');
                    }),
                    
                Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->form([
                        FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required()
                            ->helperText('Upload file Excel dengan format: kode_tabung, seri_tabung, tahun, keterangan'),
                    ])
                    ->action(function (array $data) {
                        try {
                            Excel::import(new TabungImport, $data['file']);
                            
                            Notification::make()
                                ->title('Import Berhasil')
                                ->body('Data tabung berhasil diimport.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                    
                // Action::make('download_qr_codes')
                //     ->label('Download QR Codes')
                //     ->icon('heroicon-o-qr-code')
                //     ->color('info')
                //     ->url(route('tabung.qr-codes.pdf'))
                //     ->openUrlInNewTab(),
                    
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
