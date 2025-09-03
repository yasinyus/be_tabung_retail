<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\User;
use App\Models\Pelanggan;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('trx_id')
                    ->label('Transaction IDs')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'TRX-' . strtoupper(uniqid()))
                    ->maxLength(50),
                    
                TextInput::make('user_id')
                    ->label('User ID')
                    ->default(Auth::id())
                    ->required()
                    ->hidden(),
                    
                Select::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'nama_pelanggan')
                    ->nullable(),
                    
                DateTimePicker::make('transaction_date')
                    ->label('Transaction Date')
                    ->required()
                    ->default(now())
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
                    
                TextInput::make('total')
                    ->label('Total Amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->step(0.01),
                    
                Select::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Bank Transfer',
                        'ewallet' => 'E-Wallet',
                        'credit' => 'Credit',
                    ])
                    ->required(),
                    
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ])
                    ->default('pending')
                    ->required(),
                    
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
