<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransactionFormSimple
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('trx_id')
                    ->label('Transaction IDs')
                    ->required()
                    ->maxLength(50),
                    
                TextInput::make('user_id')
                    ->label('User ID')
                    ->required()
                    ->readonly(),
                    
                TextInput::make('customer_id')
                    ->label('Customer ID')
                    ->nullable(),
                    
                DateTimePicker::make('transaction_date')
                    ->label('Transaction Date')
                    ->required()
                    ->seconds(false),
                    
                Select::make('type')
                    ->label('Transaction Type')
                    ->options([
                        'sale' => 'Sale',
                        'purchase' => 'Purchase', 
                        'refund' => 'Refund',
                    ])
                    ->default('sale')
                    ->required(),
                    
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                    
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->nullable(),
                    
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
