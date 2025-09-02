<?php

namespace App\Filament\Resources\Deposits\Schemas;

use App\Models\Pelanggan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepositForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('pelanggan_id')
                    ->label('Pelanggan')
                    ->options(function () {
                        return Pelanggan::all()->pluck('nama_pelanggan', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $pelanggan = Pelanggan::find($state);
                            if ($pelanggan) {
                                $set('kode_pelanggan', $pelanggan->kode_pelanggan);
                                $set('nama_pelanggan', $pelanggan->nama_pelanggan);
                            }
                        }
                    }),
                    
                TextInput::make('kode_pelanggan')
                    ->label('Kode Pelanggan')
                    ->disabled()
                    ->dehydrated(true),
                    
                TextInput::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->disabled()
                    ->dehydrated(true),
                    
                TextInput::make('saldo')
                    ->label('Deposit/Saldo')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->step(0.01),
                    
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),
                    
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
