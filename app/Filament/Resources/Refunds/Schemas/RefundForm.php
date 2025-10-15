<?php

namespace App\Filament\Resources\Refunds\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\SerahTerimaTabung;
use App\Models\Pelanggan;

class RefundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Refund')
                    ->schema([
                        TextInput::make('bast_id')
                            ->label('BAST ID')
                            ->placeholder('Masukkan ID BAST')
                            ->helperText('ID Berita Acara Serah Terima')
                            ->reactive()
                            ->afterStateUpdated(function ($set, ?string $state) {
                                if ($state) {
                                    // Cari data serah terima berdasarkan BAST ID
                                    $serahTerima = SerahTerimaTabung::where('bast_id', $state)->first();
                                    
                                    if ($serahTerima && $serahTerima->kode_pelanggan) {
                                        // Cari data pelanggan
                                        $pelanggan = Pelanggan::where('kode_pelanggan', $serahTerima->kode_pelanggan)->first();
                                        
                                        if ($pelanggan) {
                                            $set('kode_pelanggan', $pelanggan->kode_pelanggan);
                                            $set('harga_per_m3', $pelanggan->harga_tabung);
                                        }
                                    }
                                }
                            }),
                        
                        TextInput::make('kode_pelanggan')
                            ->label('Kode Pelanggan')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Otomatis terisi dari BAST ID'),
                        
                        TextInput::make('harga_per_m3')
                            ->label('Harga per m³')
                            ->disabled()
                            ->dehydrated()
                            ->prefix('Rp')
                            ->numeric()
                            ->helperText('Otomatis terisi dari data pelanggan'),
                            
                        TextInput::make('volume')
                            ->label('Volume (m³)')
                            ->numeric()
                            ->suffix('m³')
                            ->step(0.01)
                            ->default(0.0)
                            ->placeholder('0.00')
                            ->helperText('Volume gas dalam meter kubik (opsional untuk auto-calculate)')
                            ->reactive()
                            ->afterStateUpdated(function ($set, $get, ?string $state) {
                                $volume = floatval($state);
                                $hargaPerM3 = floatval($get('harga_per_m3'));
                                
                                // Hanya auto-calculate jika volume dan harga per m3 ada
                                if ($volume > 0 && $hargaPerM3 > 0) {
                                    $totalRefund = $volume * $hargaPerM3;
                                    $set('total_refund', number_format($totalRefund, 2, '.', ''));
                                }
                                // Jika volume 0 atau kosong, tidak mengubah total_refund
                                // Biarkan user mengisi manual
                            }),
                            
                        TextInput::make('total_refund')
                            ->label('Total Refund')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->step(0.01)
                            ->default(0.0)
                            ->placeholder('0.00')
                            ->helperText('Total nilai refund (otomatis dari volume × harga per m³, atau isi manual)'),
                            
                        Select::make('status_refund')
                            ->label('Status Refund')
                            ->options([
                                'Pending' => 'Pending',
                                'Diproses' => 'Diproses',
                                'Selesai' => 'Selesai',
                                'Dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('Pending')
                            ->required()
                            ->helperText('Status pemrosesan refund'),
                    ])
                    ->columns(2),
            ]);
    }
}
