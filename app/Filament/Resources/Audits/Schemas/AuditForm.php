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

                TextInput::make('tabung')
                    ->label('Tabung')
                    ->placeholder('Masukkan kode tabung atau daftar tabung')
                    ->maxLength(1000)
                    ->columnSpanFull()
                    ->helperText('Contoh: TBG001, TBG002, TBG003 atau format lainnya'),

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
