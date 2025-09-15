<?php

namespace App\Filament\Resources\VolumeTabungResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\Tabung;

class VolumeTabungForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kode_tabung')
                    ->label('Kode Tabung')
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return Tabung::all()->pluck('full_info', 'kode_tabung');
                    })
                    ->placeholder('Pilih tabung')
                    ->helperText('Pilih tabung dari daftar yang tersedia'),
                    
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'Kosong' => 'Kosong',
                        'Isi' => 'Isi',
                    ])
                    ->default('Kosong')
                    ->helperText('Status isi tabung'),
                    
                TextInput::make('volume')
                    ->label('Volume (m³)')
                    ->numeric()
                    ->step(0.01)
                    ->placeholder('0.00')
                    ->helperText('Volume tabung dalam liter (contoh: 12.50)')
                    ->suffix('L'),
                    
                TextInput::make('lokasi')
                    ->label('Lokasi Manual')
                    ->maxLength(255)
                    ->placeholder('Opsional: untuk tabung non-GD/PU/PA')
                    ->helperText('Kosongkan jika tabung berawalan GD (dari gudang) atau PU/PA (dari pelanggan)'),
                    
                DateTimePicker::make('tanggal_update')
                    ->label('Tanggal Update')
                    ->default(now())
                    ->required()
                    ->displayFormat('d/m/Y H:i')
                    ->helperText('Tanggal dan waktu update stok'),
            ]);
    }
}
