<?php

namespace App\Filament\Resources\VolumeTabungResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class VolumeTabungForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->required()
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->label('Tanggal')
                    ->placeholder('Pilih tanggal')
                    ->helperText('Format: DD/MM/YYYY'),
                    
                TextInput::make('lokasi')
                    ->required()
                    ->maxLength(255)
                    ->label('Lokasi')
                    ->placeholder('Contoh: Gudang A')
                    ->helperText('Lokasi pengukuran volume'),
                    
                Repeater::make('tabung')
                    ->schema([
                        TextInput::make('qr_code')
                            ->label('QR Code / ID Tabung')
                            ->required()
                            ->placeholder('Contoh: TBG001'),
                        Select::make('status')
                            ->label('Status Tabung')
                            ->required()
                            ->options([
                                'kosong' => 'Kosong',
                                'isi' => 'Isi',
                            ])
                            ->placeholder('Pilih status')
                            ->helperText('Status isi tabung'),
                    ])
                    ->columns(2)
                    ->addActionLabel('Tambah Tabung')
                    ->label('Daftar Tabung')
                    ->collapsible()
                    ->defaultItems(1)
                    ->helperText('Daftar tabung dan status isinya'),
                    
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Petugas')
                    ->placeholder('Contoh: Ahmad Suryadi')
                    ->helperText('Nama petugas yang melakukan pengecekan'),
                    
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Keterangan tambahan (opsional)')
                    ->helperText('Catatan atau keterangan tambahan tentang pengecekan')
                    ->rows(3)
                    ->maxLength(500),
            ]);
    }
}
