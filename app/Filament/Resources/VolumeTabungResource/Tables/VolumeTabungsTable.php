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
                    
                TextColumn::make('volume')
                    ->label('Volume')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' m³ ')
                    ->placeholder('-')
                    ->sortable(),
                    
                TextColumn::make('lokasi_nama')
                    ->label('Lokasi')
                    ->getStateUsing(function ($record) {
                        // Menggunakan data dari JOIN query
                        if (!empty($record->nama_gudang)) {
                            return $record->nama_gudang;
                        } elseif (!empty($record->nama_pelanggan)) {
                            return $record->nama_pelanggan;
                        }
                        return $record->lokasi ?? 'Tidak diketahui';
                    })
                    ->searchable(['lokasi', 'gudangs.nama_gudang', 'pelanggans.nama_pelanggan'])
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
                    
                SelectFilter::make('volume_filter')
                    ->label('Filter Volume')
                    ->options([
                        'bervolume' => 'Ada Volume',
                        'tanpa_volume' => 'Tanpa Volume',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'bervolume') {
                            return $query->whereNotNull('volume')->where('volume', '>', 0);
                        } elseif ($data['value'] === 'tanpa_volume') {
                            return $query->whereNull('volume')->orWhere('volume', 0);
                        }
                        return $query;
                    })
                    ->placeholder('Semua Volume'),
            ])
            ->actions([
                // Actions akan ditambahkan nanti
            ])
            ->bulkActions([
                // Bulk actions akan ditambahkan nanti
            ])
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->leftJoin('gudangs', function($join) {
                        $join->on('stok_tabung.lokasi', '=', 'gudangs.kode_gudang')
                             ->where('stok_tabung.lokasi', 'like', 'GD%');
                    })
                    ->leftJoin('pelanggans', function($join) {
                        $join->on('stok_tabung.lokasi', '=', 'pelanggans.kode_pelanggan')
                             ->where(function($query) {
                                 $query->where('stok_tabung.lokasi', 'like', 'PU%')
                                       ->orWhere('stok_tabung.lokasi', 'like', 'PA%');
                             });
                    })
                    ->leftJoin('tabungs', 'stok_tabung.kode_tabung', '=', 'tabungs.kode_tabung')
                    ->select(
                        'stok_tabung.*',
                        'gudangs.nama_gudang',
                        'pelanggans.nama_pelanggan',
                        'tabungs.seri_tabung'
                    );
            })
            ->defaultSort('tanggal_update', 'desc')
            ->emptyStateHeading('Belum ada data stok tabung')
            ->emptyStateDescription('Belum ada data stok tabung yang tercatat dalam sistem.');
    }
}
