<?php

namespace App\Filament\Resources\Refunds\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RefundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('bast_id')
                    ->label('BAST ID')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                    
                TextColumn::make('total_refund')
                    ->label('Total Refund')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),
                    
                TextColumn::make('status_refund')
                    ->label('Status Refund')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Diproses' => 'info',
                        'Selesai' => 'success',
                        'Dibatalkan' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_refund')
                    ->label('Status Refund')
                    ->options([
                        'Pending' => 'Pending',
                        'Diproses' => 'Diproses',
                        'Selesai' => 'Selesai',
                        'Dibatalkan' => 'Dibatalkan',
                    ]),
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
