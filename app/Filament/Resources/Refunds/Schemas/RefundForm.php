<?php

namespace App\Filament\Resources\Refunds\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RefundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Refund')
                    ->schema([
                        TextInput::make('bast_id')
                            ->label('BAST ID')
                            ->placeholder('Masukkan ID BAST')
                            ->helperText('ID Berita Acara Serah Terima'),
                            
                        TextInput::make('total_refund')
                            ->label('Total Refund')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->step(0.01)
                            ->default(0.0)
                            ->placeholder('0.00')
                            ->helperText('Total nilai refund dalam rupiah'),
                            
                        Select::make('status_refund')
                            ->label('Status Refund')
                            ->options([
                                'Pending' => 'Pending',
                                'Diproses' => 'Diproses',
                                'Selesai' => 'Selesai',
                                'Dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('Pending')
                            ->required()
                            ->helperText('Status pemrosesan refund'),
                    ])
                    ->columns(2),
            ]);
    }
}
