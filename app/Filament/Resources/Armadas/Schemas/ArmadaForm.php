<?php

namespace App\Filament\Resources\Armadas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ArmadaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_kendaraan')
                    ->label('Kode Kendaraan')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->placeholder('Contoh: KC0001')
                    ->helperText('Kode unik untuk kendaraan')
                    ->validationAttribute('kode kendaraan'),
                    
                TextInput::make('nopol')
                    ->label('Nomor Polisi')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(15)
                    ->placeholder('Contoh: B 1234 ABC')
                    ->helperText('Nomor polisi kendaraan (unik)')
                    ->rule('regex:/^[A-Z]{1,2}\s?\d{1,4}\s?[A-Z]{1,3}$/i')
                    ->validationAttribute('nomor polisi'),
                    
                TextInput::make('kapasitas')
                    ->label('Kapasitas (Tabung)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(500)
                    ->placeholder('Contoh: 50')
                    ->helperText('Kapasitas angkut dalam tabung')
                    ->suffix('tabung'),
                    
                Select::make('tahun')
                    ->label('Tahun Pembuatan')
                    ->required()
                    ->options(function () {
                        $currentYear = date('Y');
                        $startYear = $currentYear - 30; // 30 tahun ke belakang
                        $endYear = $currentYear + 2;    // 2 tahun ke depan
                        
                        $years = [];
                        for ($year = $endYear; $year >= $startYear; $year--) {
                            $years[$year] = $year;
                        }
                        
                        return $years;
                    })
                    ->default(date('Y'))
                    ->searchable(),
                    
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Tambahkan keterangan atau catatan khusus untuk armada ini...')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
