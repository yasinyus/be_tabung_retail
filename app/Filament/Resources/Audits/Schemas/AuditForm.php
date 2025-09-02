<?php

namespace App\Filament\Resources\Audits\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\Tabung;
use Illuminate\Support\Facades\Auth;

class AuditForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->displayFormat('d-m-Y')
                    ->format('Y-m-d')
                    ->required()
                    ->default(now()),

                TextInput::make('lokasi')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255),

                Repeater::make('tabung')
                    ->label('Tabung')
                    ->required()
                    ->schema([
                        Select::make('kode_tabung')
                            ->label('Pilih Tabung')
                            ->searchable()
                            ->options(function () {
                                return Tabung::pluck('kode_tabung', 'kode_tabung');
                            })
                            ->required(),
                        
                        TextInput::make('kondisi')
                            ->label('Kondisi')
                            ->placeholder('Baik, Rusak, Perlu Perbaikan, dll')
                            ->maxLength(255),

                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Catatan khusus untuk tabung ini')
                            ->maxLength(500),
                    ])
                    ->columns(3)
                    ->minItems(1)
                    ->addActionLabel('Tambah Tabung'),

                TextInput::make('nama')
                    ->label('Nama Auditor')
                    ->default(Auth::user()->name ?? '')
                    ->required()
                    ->maxLength(255),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Keterangan umum audit')
                    ->columnSpanFull()
                    ->maxLength(1000),
            ]);
    }
}
