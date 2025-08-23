<?php

namespace App\Filament\Resources\Pelanggans\Tables;

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

class PelanggansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_pelanggan')
                    ->label('Kode Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kode pelanggan disalin!')
                    ->badge()
                    ->color('primary'),
                    
                TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->nama_pelanggan),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email disalin!')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                BadgeColumn::make('jenis_pelanggan')
                    ->label('Jenis')
                    ->colors([
                        'primary' => 'umum',
                        'success' => 'agen',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                    
                TextColumn::make('harga_tabung')
                    ->label('Harga Tabung')
                    ->money('IDR')
                    ->sortable(),
                    
                TextColumn::make('lokasi_pelanggan')
                    ->label('Lokasi')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->lokasi_pelanggan)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('penanggung_jawab')
                    ->label('Penanggung Jawab')
                    ->limit(30)
                    ->placeholder('Tidak ada')
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
                SelectFilter::make('jenis_pelanggan')
                    ->label('Filter Jenis Pelanggan')
                    ->options([
                        'umum' => 'Umum',
                        'agen' => 'Agen',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('qr_code')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->modalHeading(fn ($record) => "QR Code - {$record->kode_pelanggan}")
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
                                     alt='QR Code for {$record->kode_pelanggan}' 
                                     class='mx-auto border rounded-lg shadow-lg'
                                     style='max-width: 300px; width: 100%;'>
                                <div class='mt-4 text-sm text-gray-600'>
                                    <p><strong>Kode Pelanggan:</strong> {$record->kode_pelanggan}</p>
                                    <p><strong>Nama:</strong> {$record->nama_pelanggan}</p>
                                    <p><strong>Jenis:</strong> " . ucfirst($record->jenis_pelanggan) . "</p>
                                    <p><strong>Email:</strong> {$record->email}</p>
                                </div>
                                <div class='mt-4 text-xs text-gray-500'>
                                    Scan QR Code ini untuk melihat detail pelanggan
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
