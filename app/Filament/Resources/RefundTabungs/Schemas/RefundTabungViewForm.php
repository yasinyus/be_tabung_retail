<?php

namespace App\Filament\Resources\RefundTabungs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Pelanggan;

class RefundTabungViewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Refund')
                    ->schema([
                        TextInput::make('bast_id')
                            ->label('BAST ID')
                            ->disabled()
                            ->placeholder('ID Berita Acara Serah Terima'),

                        Select::make('kode_pelanggan')
                            ->label('Pelanggan')
                            ->disabled()
                            ->options(function () {
                                return Pelanggan::all()->pluck('nama_pelanggan', 'kode_pelanggan');
                            })
                            ->placeholder('Pilih pelanggan'),

                        TextInput::make('nama_pelanggan')
                            ->label('Nama Pelanggan')
                            ->disabled()
                            ->placeholder('Nama pelanggan akan otomatis terisi'),

                        Select::make('status')
                            ->label('Status')
                            ->disabled()
                            ->options([
                                'Rusak' => 'Rusak',
                                'Hilang' => 'Hilang',
                                'Kembali' => 'Kembali',
                            ])
                            ->placeholder('Pilih status'),
                    ])
                    ->collapsible()
                    ->columns(2),

                Section::make('Daftar Kode Tabung')
                    ->schema([
                        Repeater::make('tabung')
                            ->label('')
                            ->schema([
                                TextInput::make('kode_tabung')
                                    ->label('Kode Tabung')
                                    ->disabled()
                                    ->required(),
                            ])
                            ->disabled()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->itemLabel(function (array $state): ?string {
                                return $state['kode_tabung'] ?? 'Tabung';
                            })
                            ->helperText('Daftar kode tabung yang akan di-refund')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}