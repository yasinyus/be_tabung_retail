<?php

namespace App\Filament\Resources\Gudangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use App\Filament\Resources\Gudangs\GudangResource;
use App\Exports\GudangExport;
use App\Exports\GudangTemplateExport;
use App\Imports\GudangImport;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class GudangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_gudang')
                    ->label('Kode Gudang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kode gudang disalin!')
                    ->badge()
                    ->color('primary'),
                    
                TextColumn::make('nama_gudang')
                    ->label('Nama Gudang')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->nama_gudang),
                    
                BadgeColumn::make('tahun_gudang')
                    ->label('Tahun Dibangun')
                    ->colors([
                        'danger' => fn ($state) => $state < date('Y') - 30,
                        'warning' => fn ($state) => $state < date('Y') - 20,
                        'success' => fn ($state) => $state >= date('Y') - 20,
                    ])
                    ->sortable(),
                    
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->placeholder('Tidak ada keterangan')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
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
                SelectFilter::make('tahun_gudang')
                    ->label('Filter Tahun Dibangun')
                    ->options(function () {
                        $currentYear = date('Y');
                        $startYear = 1980;
                        $endYear = $currentYear + 5;
                        
                        $years = [];
                        for ($year = $endYear; $year >= $startYear; $year--) {
                            $years[$year] = $year;
                        }
                        
                        return $years;
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->form([
                        TextInput::make('kode_gudang')
                            ->label('Kode Gudang')
                            ->default(fn ($record) => $record->kode_gudang)
                            ->disabled(),
                            
                        TextInput::make('nama_gudang')
                            ->label('Nama Gudang')
                            ->default(fn ($record) => $record->nama_gudang)
                            ->disabled(),
                            
                        Select::make('tahun_gudang')
                            ->label('Tahun Dibangun')
                            ->options(function () {
                                $currentYear = date('Y');
                                $startYear = 1980;
                                $endYear = $currentYear + 5;
                                
                                $years = [];
                                for ($year = $endYear; $year >= $startYear; $year--) {
                                    $years[$year] = $year;
                                }
                                
                                return $years;
                            })
                            ->default(fn ($record) => $record->tahun_gudang)
                            ->disabled(),
                            
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->default(fn ($record) => $record->keterangan)
                            ->disabled()
                            ->rows(3),
                            
                        TextInput::make('created_at')
                            ->label('Dibuat Pada')
                            ->default(fn ($record) => $record->created_at?->format('d-m-Y H:i:s'))
                            ->disabled(),
                            
                        TextInput::make('updated_at')
                            ->label('Diupdate Pada')
                            ->default(fn ($record) => $record->updated_at?->format('d-m-Y H:i:s'))
                            ->disabled(),
                    ])
                    ->modalHeading(fn ($record) => 'Detail Gudang - ' . $record->kode_gudang)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('4xl'),
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => GudangResource::getUrl('edit', ['record' => $record]))
                    ->color('warning'),
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn ($record) => "QR Code - {$record->kode_gudang}")
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
                                     alt='QR Code for {$record->kode_gudang}' 
                                     class='mx-auto border rounded-lg shadow-lg'
                                     style='max-width: 300px; width: 100%;'>
                                <div class='mt-4 text-sm text-gray-600'>
                                    <p><strong>Kode Gudang:</strong> {$record->kode_gudang}</p>
                                    <p><strong>Nama Gudang:</strong> {$record->nama_gudang}</p>
                                    <p><strong>Tahun Dibangun:</strong> {$record->tahun_gudang}</p>
                                </div>
                                <div class='mt-4 text-xs text-gray-500'>
                                    Scan QR Code ini untuk melihat detail gudang
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
                        return Excel::download(new GudangTemplateExport, 'template-gudang.xlsx');
                    }),
                    
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new GudangExport, 'gudang-' . date('Y-m-d') . '.xlsx');
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
                            ->helperText('Upload file Excel dengan format: kode_gudang, nama_gudang, tahun_gudang, keterangan'),
                    ])
                    ->action(function (array $data) {
                        try {
                            Excel::import(new GudangImport, $data['file']);
                            
                            Notification::make()
                                ->title('Import Berhasil')
                                ->body('Data gudang berhasil diimport.')
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
                    
                Action::make('download_qr_codes')
                    ->label('Download QR Codes')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->url(route('gudang.qr-codes.pdf'))
                    ->openUrlInNewTab(),
                    
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
