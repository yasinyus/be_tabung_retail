<?php

namespace App\Filament\Resources\Audits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tabung')
                    ->label('Tabung')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            $codes = collect($state)->pluck('kode_tabung')->filter()->values();
                            if ($codes->count() > 2) {
                                return $codes->take(2)->implode(', ') . ' (+' . ($codes->count() - 2) . ' lainnya)';
                            }
                            return $codes->implode(', ');
                        }
                        return '-';
                    })
                    ->limit(50)
                    ->tooltip(function ($state) {
                        if (is_array($state)) {
                            return collect($state)->pluck('kode_tabung')->filter()->implode(', ');
                        }
                        return null;
                    }),

                TextColumn::make('nama')
                    ->label('Nama Auditor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(function ($state) {
                        return $state;
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('nama')
                    ->label('Auditor')
                    ->options(function () {
                        return \App\Models\Audit::distinct('nama')->pluck('nama', 'nama');
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
