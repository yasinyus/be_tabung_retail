<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trx_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('customer.nama_pelanggan')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No Customer'),
                    
                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                BadgeColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sale' => 'Sale',
                        'purchase' => 'Purchase',
                        'refund' => 'Refund',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'sale',
                        'info' => 'purchase', 
                        'warning' => 'refund',
                    ]),
                    
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                    
                BadgeColumn::make('payment_method')
                    ->label('Payment')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                        'ewallet' => 'E-Wallet',
                        'credit' => 'Credit',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'cash',
                        'info' => 'transfer',
                        'warning' => 'ewallet',
                        'danger' => 'credit',
                    ]),
                    
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                        'info' => 'refunded',
                    ]),
                    
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Transaction Type')
                    ->options([
                        'sale' => 'Sale',
                        'purchase' => 'Purchase',
                        'refund' => 'Refund',
                    ]),
                    
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
                    
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Bank Transfer',
                        'ewallet' => 'E-Wallet',
                        'credit' => 'Credit',
                    ]),
                    
                Filter::make('transaction_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('transaction_date', 'desc');
    }
}
