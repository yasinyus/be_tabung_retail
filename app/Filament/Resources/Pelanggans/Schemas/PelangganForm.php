<?php

namespace App\Filament\Resources\Pelanggans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_pelanggan')
                    ->label('Kode Pelanggan')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->placeholder('Contoh: PLG-001')
                    ->helperText('Kode unik untuk identifikasi pelanggan'),

                TextInput::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: PT Maju Jaya'),

                Textarea::make('lokasi_pelanggan')
                    ->label('Lokasi Pelanggan')
                    ->required()
                    ->placeholder('Alamat lengkap pelanggan...')
                    ->rows(3),

                TextInput::make('harga_tabung')
                    ->label('Harga per m3')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('0')
                    ->helperText('Harga dalam Rupiah'),

                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->placeholder('email@example.com'),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->placeholder('Minimal 8 karakter'),

                Select::make('jenis_pelanggan')
                    ->label('Jenis Pelanggan')
                    ->required()
                    ->options([
                        'umum' => 'Umum',
                        'agen' => 'Agen',
                    ])
                    ->default('umum')
                    ->helperText('Pilih jenis pelanggan'),

                TextInput::make('penanggung_jawab')
                    ->label('Penanggung Jawab')
                    ->maxLength(255)
                    ->placeholder('Nama penanggung jawab (opsional)'),
            ]);
    }
}
