<?php

namespace App\Filament\Resources\RefundTabungs\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RefundTabungs\RefundTabungResource;
use App\Models\Pelanggan;

class RefundTabungsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->sortable()
                    ->size('sm')
                    ->alignCenter(),

                TextColumn::make('bast_id')
                    ->label('BAST ID')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('kode_pelanggan')
                    ->label('Kode Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->getStateUsing(function ($record) {
                        return $record->pelanggan ? $record->pelanggan->nama_pelanggan : $record->kode_pelanggan;
                    })
                    ->searchable(['pelanggans.nama_pelanggan'])
                    ->sortable(),

                TextColumn::make('daftar_tabung')
                    ->label('Daftar Tabung')
                    ->getStateUsing(function ($record) {
                        if (is_array($record->tabung) && !empty($record->tabung)) {
                            // Handle both string array and object array formats
                            $tabungList = [];
                            foreach ($record->tabung as $tabung) {
                                if (is_string($tabung)) {
                                    $tabungList[] = $tabung;
                                } elseif (is_array($tabung) && isset($tabung['kode_tabung'])) {
                                    $tabungList[] = $tabung['kode_tabung'];
                                }
                            }
                            return implode(', ', $tabungList);
                        }
                        return '-';
                    })
                    ->limit(50)
                    ->tooltip(function ($record) {
                        if (is_array($record->tabung) && !empty($record->tabung)) {
                            $tabungList = [];
                            foreach ($record->tabung as $tabung) {
                                if (is_string($tabung)) {
                                    $tabungList[] = $tabung;
                                } elseif (is_array($tabung) && isset($tabung['kode_tabung'])) {
                                    $tabungList[] = $tabung['kode_tabung'];
                                }
                            }
                            return implode(', ', $tabungList);
                        }
                        return 'Tidak ada tabung';
                    }),

                TextColumn::make('jumlah_tabung')
                    ->label('Jumlah')
                    ->getStateUsing(function ($record) {
                        return is_array($record->tabung) ? count($record->tabung) : 0;
                    })
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Rusak' => 'danger',
                        'Refund' => 'primary',
                        'Selesai' => 'success',
                        'Diproses' => 'warning',
                        'Pending' => 'danger',
                        'Dibatalkan' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('serah_terima_tabungs.status', 'Rusak')
                    ->leftJoin('pelanggans', function($join) {
                        $join->whereRaw('serah_terima_tabungs.kode_pelanggan COLLATE utf8mb4_unicode_ci = pelanggans.kode_pelanggan');
                    })
                    ->select('serah_terima_tabungs.*', 'pelanggans.nama_pelanggan');
            })
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Rusak' => 'Rusak',
                        'Refund' => 'Refund',
                        'Selesai' => 'Selesai',
                        'Diproses' => 'Diproses',
                        'Pending' => 'Pending',
                        'Dibatalkan' => 'Dibatalkan',
                    ])
                    ->placeholder('Semua Status'),

                SelectFilter::make('kode_pelanggan')
                    ->label('Filter Pelanggan')
                    ->options(function () {
                        return Pelanggan::all()->pluck('nama_pelanggan', 'kode_pelanggan')->toArray();
                    })
                    ->searchable()
                    ->placeholder('Semua Pelanggan'),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => RefundTabungResource::getUrl('view', ['record' => $record]))
                    ->color('info'),

                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => RefundTabungResource::getUrl('edit', ['record' => $record]))
                    ->color('warning'),

                Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->delete()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('Belum ada data refund tabung')
            ->emptyStateDescription('Belum ada data refund tabung yang tercatat dalam sistem.')
            ->emptyStateIcon('heroicon-o-arrow-path');
    }
}