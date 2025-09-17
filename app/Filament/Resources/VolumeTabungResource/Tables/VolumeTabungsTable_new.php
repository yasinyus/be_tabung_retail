<?php

namespace App\Filament\Resources\VolumeTabungResource\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class VolumeTabungsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_tabung')
                    ->label('Kode Tabung')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('tabung.seri_tabung')
                    ->label('Seri Tabung')
                    ->searchable(),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Kosong' => 'danger',
                        'Isi' => 'success',
                        default => 'gray',
                    }),
                    
                TextColumn::make('posisi')
                    ->label('Posisi/Lokasi')
                    ->searchable()
                    ->placeholder('Tidak diketahui'),
                    
                TextColumn::make('tanggal_update')
                    ->label('Terakhir Update')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Kosong' => 'Kosong',
                        'Isi' => 'Isi',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->actions([
                // Actions akan ditambahkan nanti
            ])
            ->bulkActions([
                // Bulk actions akan ditambahkan nanti
            ])
            ->defaultSort('tanggal_update', 'desc')
            ->emptyStateHeading('Belum ada data stok tabung')
            ->emptyStateDescription('Belum ada data stok tabung yang tercatat dalam sistem.');
    }
}
