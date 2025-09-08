<?php

namespace App\Filament\Resources\VolumeTabungResource\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class VolumeTabungsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->label('Tanggal'),
                    
                TextColumn::make('lokasi')
                    ->searchable()
                    ->label('Lokasi'),
                    
                TextColumn::make('tabung')
                    ->getStateUsing(function ($record) {
                        $tabungData = $record->tabung;
                        if (is_array($tabungData)) {
                            return (string) count($tabungData);
                        }
                        if (is_string($tabungData)) {
                            $decoded = json_decode($tabungData, true);
                            if (is_array($decoded)) {
                                return (string) count($decoded);
                            }
                        }
                        return '0';
                    })
                    ->numeric()
                    ->sortable()
                    ->label('Jumlah Tabung'),
                    
                TextColumn::make('keterangan')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        
                        return $state;
                    })
                    ->label('Keterangan')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('status')
                    ->getStateUsing(function ($record) {
                        $tabungData = $record->tabung;
                        $statusCounts = ['kosong' => 0, 'isi' => 0];
                        
                        if (is_array($tabungData)) {
                            foreach ($tabungData as $tabung) {
                                $status = $tabung['status'] ?? 'kosong';
                                if (isset($statusCounts[$status])) {
                                    $statusCounts[$status]++;
                                }
                            }
                        } elseif (is_string($tabungData)) {
                            $decoded = json_decode($tabungData, true);
                            if (is_array($decoded)) {
                                foreach ($decoded as $tabung) {
                                    $status = $tabung['status'] ?? 'kosong';
                                    if (isset($statusCounts[$status])) {
                                        $statusCounts[$status]++;
                                    }
                                }
                            }
                        }
                        
                        return "Isi: {$statusCounts['isi']}, Kosong: {$statusCounts['kosong']}";
                    })
                    ->searchable()
                    ->label('Status Tabung'),
                    
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat'),
                    
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui'),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->label('Filter Tanggal'),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Lihat')
                    ->icon('heroicon-o-eye'),
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih'),
                ]),
            ])
            ->defaultSort('tanggal', 'desc')
            ->emptyStateHeading('Belum ada data status tabung')
            ->emptyStateDescription('Belum ada data volume tabung yang tercatat dalam sistem.')
            ->striped();
    }
}
