<?php

namespace App\Filament\Resources\TabungActivityResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class TabungActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_aktivitas')
                    ->label('Nama Aktivitas')
                    ->required()
                    ->maxLength(255)
                    ->default('Terima Tabung')
                    ->placeholder('Contoh: Terima Tabung Dari Armada')
                    ->helperText('Jenis aktivitas yang dilakukan'),

                Select::make('id_user')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Pilih user yang melakukan aktivitas'),

                TextInput::make('nama_petugas')
                    ->label('Nama Petugas')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Nama petugas yang melakukan aktivitas'),

                TextInput::make('dari')
                    ->label('Dari')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: GDG-002 atau B 5678 DEF')
                    ->helperText('Asal lokasi atau armada'),

                TextInput::make('tujuan')
                    ->label('Tujuan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: GDG-001 atau B 1234 ABC')
                    ->helperText('Tujuan lokasi atau armada'),

                Select::make('status')
                    ->label('Status Tabung')
                    ->options([
                        'Pending' => 'Pending',
                        'Kosong' => 'Kosong',
                        'Isi' => 'Isi',
                    ])
                    ->required()
                    ->default('Pending')
                    ->helperText('Status tabung saat aktivitas'),

                TextInput::make('total_tabung')
                    ->label('Total Tabung')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->helperText('Jumlah total tabung'),

                TextInput::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()->format('d/m/Y'))
                    ->helperText('Tanggal aktivitas (format: dd/mm/yyyy)'),

                Repeater::make('tabung')
                    ->label('Daftar Tabung')
                    ->schema([
                        TextInput::make('qr_code')
                            ->label('QR Code/ID Tabung')
                            ->required()
                            ->placeholder('Scan atau masukkan QR code tabung'),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Tabung')
                    ->reorderable()
                    ->collapsible()
                    ->helperText('Daftar QR code atau ID tabung yang terlibat dalam aktivitas'),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Masukkan keterangan tambahan jika diperlukan')
                    ->helperText('Keterangan opsional untuk aktivitas ini'),
            ]);
    }
}
