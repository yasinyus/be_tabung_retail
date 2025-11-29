<?php

namespace App\Filament\Resources\VolumeTabungResource\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Gudang;
use Illuminate\Database\Eloquent\Builder;

class VolumeTabungsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_tabung')
                    ->label('Kode Tabung')
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('stok_tabung.kode_tabung', 'like', "%{$search}%");
                    }),
                    
                TextColumn::make('tabung.seri_tabung')
                    ->label('Seri Tabung')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('tabungs.seri_tabung', 'like', "%{$search}%");
                    }),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Kosong' => 'danger',
                        'Isi' => 'success',
                        'Rusak' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('stok_tabung.status', 'like', "%{$search}%");
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
                        } elseif (!empty($record->armada_nopol)) {
                            return $record->armada_nopol;
                        }
                        return $record->lokasi ?? 'Tidak diketahui';
                    })
                    ->placeholder('Tidak diketahui')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($query) use ($search) {
                            $query->where('gudangs.nama_gudang', 'like', "%{$search}%")
                                  ->orWhere('pelanggans.nama_pelanggan', 'like', "%{$search}%")
                                  ->orWhere('stok_tabung.status', 'like', "%{$search}%")
                                  ->orWhere('stok_tabung.lokasi', 'like', "%{$search}%");
                        });
                    }),
                    
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
                SelectFilter::make('lokasi_gudang')
                    ->label('Filter Gudang')
                    ->options(function () {
                        return Gudang::pluck('nama_gudang', 'kode_gudang')->toArray();
                    })
                    ->query(function ($query, $data) {
                        if (!empty($data['value'])) {
                            return $query->where('stok_tabung.lokasi', $data['value']);
                        }
                        return $query;
                    })
                    ->placeholder('Semua Gudang'),
                    
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Kosong' => 'Kosong',
                        'Isi' => 'Isi',
                        'Rusak' => 'Rusak',
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
            ->searchable()
            ->modifyQueryUsing(function (Builder $query, array $data = []) {
                $baseQuery = $query
                    ->leftJoin('gudangs', function($join) {
                        $join->on('stok_tabung.lokasi', '=', 'gudangs.kode_gudang')
                             ->where('stok_tabung.lokasi', 'like', 'GD%');
                    })
                    ->leftJoin('pelanggans', function($join) {
                        $join->on('stok_tabung.lokasi', '=', 'pelanggans.kode_pelanggan')
                             ->where(function($query) {
                                 // Include PU, PA and PM prefixes for pelanggan
                                 $query->where('stok_tabung.lokasi', 'like', 'PU%')
                                       ->orWhere('stok_tabung.lokasi', 'like', 'PA%')
                                       ->orWhere('stok_tabung.lokasi', 'like', 'PM%');
                             });
                    })
                    ->leftJoin('tabungs', 'stok_tabung.kode_tabung', '=', 'tabungs.kode_tabung')
                    ->leftJoin('armadas', function($join) {
                        $join->on('stok_tabung.lokasi', '=', 'armadas.nopol');
                    })
                    ->select(
                        'stok_tabung.*',
                        'gudangs.nama_gudang',
                        'pelanggans.nama_pelanggan',
                        'tabungs.seri_tabung',
                        'armadas.nopol as armada_nopol',
                    );
                
                // Handle search from URL parameter
                $searchParam = request()->get('search');
                if ($searchParam) {
                    $baseQuery->where(function ($query) use ($searchParam) {
                        $query->where('stok_tabung.kode_tabung', 'like', "%{$searchParam}%")
                              ->orWhere('stok_tabung.lokasi', 'like', "%{$searchParam}%")
                              ->orWhere('stok_tabung.status', 'like', "%{$searchParam}%")
                              ->orWhere('tabungs.seri_tabung', 'like', "%{$searchParam}%")
                              ->orWhere('gudangs.nama_gudang', 'like', "%{$searchParam}%")
                              ->orWhere('pelanggans.nama_pelanggan', 'like', "%{$searchParam}%")
                              ->orWhere('armadas.nopol', 'like', "%{$searchParam}%");
                    });
                }
                
                return $baseQuery;
            })
            ->defaultSort('tanggal_update', 'desc')
            ->emptyStateHeading('Belum ada data stok tabung')
            ->emptyStateDescription('Belum ada data stok tabung yang tercatat dalam sistem.');
    }
}
