<?php

namespace App\Filament\Resources\Tabungs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class TabungForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_tabung')
                    ->label('Kode Tabung')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('Contoh: TBG-001')
                    ->helperText('Kode unik untuk identifikasi tabung'),
                    
                TextInput::make('seri_tabung')
                    ->label('Seri Tabung')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Contoh: A123456789')
                    ->helperText('Nomor seri dari pabrik'),
                    
                Select::make('tahun')
                    ->label('Tahun Produksi')
                    ->required()
                    ->options(function () {
                        $currentYear = date('Y');
                        $startYear = $currentYear - 20; // 20 tahun ke belakang
                        $endYear = $currentYear + 5;    // 5 tahun ke depan
                        
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
                    ->placeholder('Tambahkan keterangan atau catatan khusus untuk tabung ini...')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
