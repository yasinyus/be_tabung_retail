<?php

namespace App\Filament\Resources\RefundTabungs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Pelanggan;

class RefundTabungForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Refund')
                    ->schema([
                        TextInput::make('bast_id')
                            ->label('BAST ID')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Masukkan ID BAST')
                            ->helperText('ID Berita Acara Serah Terima'),

                        Select::make('kode_pelanggan')
                            ->label('Pelanggan')
                            ->required()
                            ->options(function () {
                                return Pelanggan::all()->pluck('nama_pelanggan', 'kode_pelanggan')->toArray();
                            })
                            ->searchable()
                            ->placeholder('Pilih pelanggan')
                            ->helperText('Pilih pelanggan yang melakukan refund'),

                        Select::make('status')
                            ->label('Status Refund')
                            ->required()
                            ->options([
                                'Rusak' => 'Rusak',
                                'Refund' => 'Refund',
                                'Pending' => 'Pending',
                                'Diproses' => 'Diproses',
                                'Selesai' => 'Selesai',
                                'Dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('Rusak')
                            ->helperText('Status proses refund'),

                        TextInput::make('total_harga')
                            ->label('Total Harga Refund')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->helperText('Total harga refund dalam Rupiah'),
                    ])
                    ->columns(2),

                Section::make('Daftar Tabung Refund')
                    ->schema([
                        Repeater::make('tabung')
                            ->label('Tabung yang Direfund')
                            ->schema([
                                TextInput::make('kode_tabung')
                                    ->label('Kode Tabung')
                                    ->required()
                                    ->placeholder('TB001')
                                    ->maxLength(20),
                            ])
                            ->columns(1)
                            ->addActionLabel('Tambah Tabung')
                            ->deleteAction(
                                fn ($action) => $action
                                    ->requiresConfirmation()
                                    ->modalHeading('Hapus Tabung')
                                    ->modalDescription('Apakah Anda yakin ingin menghapus tabung ini?')
                                    ->modalSubmitActionLabel('Ya, Hapus')
                            )
                            ->reorderable(false)
                            ->collapsible()
                            ->collapsed(false)
                            ->defaultItems(1)
                            ->itemLabel(fn (array $state): ?string => 
                                !empty($state['kode_tabung']) ? $state['kode_tabung'] : 'Tabung Baru'
                            )
                            ->helperText('Klik "Tambah Tabung" untuk menambahkan tabung baru atau gunakan tombol hapus untuk menghapus tabung'),
                    ]),
            ]);
    }
}