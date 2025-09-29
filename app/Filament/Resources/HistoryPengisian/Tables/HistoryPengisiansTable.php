<?php

namespace App\Filament\Resources\HistoryPengisian\Tables;

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
use App\Filament\Resources\HistoryPengisian\HistoryPengisianResource;

class HistoryPengisiansTable
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
                    
                TextColumn::make('tanggal')
                    ->label('Tanggal Pengisian')
                    ->date('d/m/Y')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('nama')
                    ->label('Nama Petugas')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Proses' => 'warning',
                        'Pending' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('tabung_count')
                    ->label('Jumlah Tabung')
                    ->getStateUsing(function ($record) {
                        if (is_array($record->tabung)) {
                            return count($record->tabung);
                        }
                        return 0;
                    })
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
                    
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->keterangan)
                    ->placeholder('Tidak ada keterangan'),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Selesai' => 'Selesai',
                        'Proses' => 'Proses',
                        'Pending' => 'Pending',
                    ])
                    ->placeholder('Semua Status'),
                    
                SelectFilter::make('lokasi')
                    ->label('Filter Lokasi')
                    ->options(function () {
                        return \App\Models\VolumeTabung::whereNotNull('lokasi')
                            ->distinct()
                            ->pluck('lokasi', 'lokasi')
                            ->toArray();
                    })
                    ->placeholder('Semua Lokasi'),
                    
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => HistoryPengisianResource::getUrl('view', ['record' => $record]))
                    ->color('info'),
                    
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => HistoryPengisianResource::getUrl('edit', ['record' => $record]))
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
            ->defaultSort('tanggal', 'desc')
            ->striped()
            ->emptyStateHeading('Belum ada data history pengisian')
            ->emptyStateDescription('Belum ada data history pengisian tabung yang tercatat dalam sistem.')
            ->emptyStateIcon('heroicon-o-clock');
    }
}