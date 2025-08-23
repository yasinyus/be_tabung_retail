<?php

namespace App\Filament\Resources\Armadas\Tables;

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

class ArmadasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nopol')
                    ->label('Nomor Polisi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('kapasitas')
                    ->label('Kapasitas')
                    ->suffix(' ton')
                    ->numeric()
                    ->sortable(),
                    
                BadgeColumn::make('tahun')
                    ->label('Tahun')
                    ->colors([
                        'danger' => fn ($state) => $state < date('Y') - 15,
                        'warning' => fn ($state) => $state < date('Y') - 10,
                        'success' => fn ($state) => $state >= date('Y') - 10,
                    ])
                    ->sortable(),
                    
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
                        $startYear = $currentYear - 30;
                        $endYear = $currentYear + 2;
                        
                        $years = [];
                        for ($year = $endYear; $year >= $startYear; $year--) {
                            $years[$year] = $year;
                        }
                        
                        return $years;
                    }),
                SelectFilter::make('kapasitas')
                    ->label('Filter Kapasitas')
                    ->options([
                        '1' => '1 ton',
                        '3' => '3 ton',
                        '5' => '5 ton',
                        '8' => '8 ton',
                        '10' => '10 ton',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn ($record) => "QR Code - {$record->nopol}")
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
                                     alt='QR Code for {$record->nopol}' 
                                     class='mx-auto border rounded-lg shadow-lg'
                                     style='max-width: 300px; width: 100%;'>
                                <div class='mt-4 text-sm text-gray-600'>
                                    <p><strong>Nomor Polisi:</strong> {$record->nopol}</p>
                                    <p><strong>Kapasitas:</strong> {$record->kapasitas} ton</p>
                                    <p><strong>Tahun:</strong> {$record->tahun}</p>
                                </div>
                                <div class='mt-4 text-xs text-gray-500'>
                                    Scan QR Code ini untuk melihat detail armada
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
