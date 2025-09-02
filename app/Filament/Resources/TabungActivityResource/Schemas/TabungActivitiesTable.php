<?php

namespace App\Filament\Resources\TabungActivityResource\Schemas;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Schemas\Schema;

class TabungActivitiesTable
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_aktivitas')
                    ->label('Nama Aktivitas')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->weight('bold'),

                TextColumn::make('nama_petugas')
                    ->label('Petugas')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                TextColumn::make('dari')
                    ->label('Dari')
                    ->sortable()
                    ->searchable()
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 25) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->sortable()
                    ->searchable()
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 25) {
                            return null;
                        }
                        return $state;
                    }),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Pending',
                        'danger' => 'Kosong',
                        'success' => 'Isi',
                    ])
                    ->sortable(),

                TextColumn::make('total_tabung')
                    ->label('Jml Tabung')
                    ->numeric()
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->sortable()
                    ->date('d/m/Y'),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('waktu')
                    ->label('Waktu Input')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Kosong' => 'Kosong',
                        'Isi' => 'Isi',
                    ]),
                
                SelectFilter::make('nama_aktivitas')
                    ->label('Jenis Aktivitas')
                    ->options([
                        'Terima Tabung' => 'Terima Tabung',
                        'Kirim Tabung' => 'Kirim Tabung',
                        'Pindah Tabung' => 'Pindah Tabung',
                        'Maintenance' => 'Maintenance',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Detail'),
                EditAction::make()
                    ->label('Edit'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('waktu', 'desc');
    }
}
