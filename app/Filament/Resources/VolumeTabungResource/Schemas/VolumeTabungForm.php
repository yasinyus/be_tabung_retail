<?php

namespace App\Filament\Resources\VolumeTabungResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
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
                        TextInput::make('volume')
                            ->label('Volume Tabung')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->suffix('m³')
                            ->placeholder('0.00')
                            ->helperText('Volume tabung dalam meter kubik')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // Hitung total volume setiap kali ada perubahan
                                self::calculateTotalVolume($set, $get);
                            }),
                    ])
                    ->columns(2)
                    ->addActionLabel('Tambah Tabung')
                    ->label('Daftar Tabung')
                    ->collapsible()
                    ->defaultItems(1)
                    ->helperText('Daftar tabung yang diukur volumenya')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        // Hitung total volume setiap kali ada perubahan pada repeater
                        self::calculateTotalVolume($set, $get);
                    })
                    ->deleteAction(
                        fn ($action) => $action->after(function ($set, $get) {
                            // Hitung ulang total volume setelah delete
                            self::calculateTotalVolume($set, $get);
                        })
                    ),
                    
                TextInput::make('volume_total')
                    ->label('Volume Total')
                    ->suffix('m³')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->default(0)
                    ->helperText('Total volume dari semua tabung (dihitung otomatis)')
                    ->extraAttributes([
                        'style' => 'background-color: #f3f4f6; font-weight: 600; font-size: 1.1em;'
                    ]),
                    
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Petugas')
                    ->placeholder('Contoh: Ahmad Suryadi')
                    ->helperText('Nama petugas yang melakukan pengukuran'),
                    
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Keterangan tambahan (opsional)')
                    ->helperText('Catatan atau keterangan tambahan tentang pengukuran')
                    ->rows(3)
                    ->maxLength(500),
            ]);
    }
    
    /**
     * Calculate total volume from all tabung items
     */
    private static function calculateTotalVolume($set, $get): void
    {
        $tabung = $get('tabung') ?: [];
        $total = 0;
        
        foreach ($tabung as $item) {
            if (isset($item['volume']) && is_numeric($item['volume'])) {
                $total += (float) $item['volume'];
            }
        }
        
        $set('volume_total', round($total, 2));
    }
}
