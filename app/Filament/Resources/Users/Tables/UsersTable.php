<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin_utama' => 'Administrator Utama',
                        'admin_umum' => 'Administrator Umum',
                        'kepala_gudang' => 'Kepala Gudang',
                        'operator_retail' => 'Operator Retail',
                        'driver' => 'Driver',
                        'auditor' => 'Auditor',
                        'pelanggan_umum' => 'Pelanggan Umum',
                        'pelanggan_agen' => 'Pelanggan Agen',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'admin_utama',
                        'warning' => 'admin_umum',
                        'success' => 'kepala_gudang',
                        'primary' => 'operator_retail',
                        'secondary' => ['driver', 'auditor'],
                        'gray' => ['pelanggan_umum', 'pelanggan_agen'],
                    ])
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options([
                        'admin_utama' => 'Administrator Utama',
                        'admin_umum' => 'Administrator Umum',
                        'kepala_gudang' => 'Kepala Gudang',
                        'operator_retail' => 'Operator Retail',
                        'driver' => 'Driver',
                        'auditor' => 'Auditor',
                        'pelanggan_umum' => 'Pelanggan Umum',
                        'pelanggan_agen' => 'Pelanggan Agen',
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
