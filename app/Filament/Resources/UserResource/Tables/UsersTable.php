<?php

namespace App\Filament\Resources\UserResource\Tables;

use App\Filament\Resources\UserResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                    
                BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'danger' => 'admin_utama',
                        'warning' => 'admin_umum',
                        'info' => 'kepala_gudang',
                        'success' => 'operator_retail',
                        'gray' => 'driver',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin_utama' => 'Admin Utama',
                        'admin_umum' => 'Admin Umum',
                        'kepala_gudang' => 'Kepala Gudang',
                        'operator_retail' => 'Operator Retail',
                        'driver' => 'Driver',
                        default => $state,
                    })
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options([
                        'admin_utama' => 'Admin Utama',
                        'admin_umum' => 'Admin Umum',
                        'kepala_gudang' => 'Kepala Gudang',
                        'operator_retail' => 'Operator Retail',
                        'driver' => 'Driver',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record]))
                    ->color('info'),
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record]))
                    ->color('warning'),
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->delete()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
