<?php

namespace App\Filament\Resources\Gudangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

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
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
