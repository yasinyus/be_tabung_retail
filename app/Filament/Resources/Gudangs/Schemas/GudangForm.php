<?php

namespace App\Filament\Resources\Gudangs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class GudangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_gudang')
                    ->label('Kode Gudang')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->placeholder('Contoh: GDG-001')
                    ->helperText('Kode unik untuk identifikasi gudang'),

                TextInput::make('nama_gudang')
                    ->label('Nama Gudang')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Gudang Pusat Jakarta')
                    ->helperText('Nama lengkap gudang'),

                Select::make('tahun_gudang')
                    ->label('Tahun Dibangun')
                    ->required()
                    ->options(function () {
                        $currentYear = date('Y');
                        $startYear = 1980;
                        $endYear = $currentYear + 5;
                        
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
                    ->placeholder('Deskripsi tambahan tentang gudang...')
                    ->maxLength(1000)
                    ->rows(3)
                    ->helperText('Informasi tambahan (opsional)'),
            ]);
    }
}
