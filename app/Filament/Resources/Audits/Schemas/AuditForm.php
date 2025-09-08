<?php

namespace App\Filament\Resources\Audits\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
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

                Repeater::make('tabung_items')
                    ->label('Daftar Tabung')
                    ->schema([
                        TextInput::make('qr_code')
                            ->label('QR Code/ID Tabung')
                            ->required()
                            ->placeholder('Scan atau masukkan QR code tabung'),
                        
                        Select::make('status')
                            ->label('Status Tabung')
                            ->options([
                                'Kosong' => 'Kosong',
                                'Isi' => 'Isi',
                            ])
                            ->required()
                            ->default('Kosong')
                            ->helperText('Status tabung saat audit'),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Tabung')
                    ->reorderable()
                    ->collapsible()
                    ->columnSpanFull()
                    ->helperText('Daftar QR code atau ID tabung yang diaudit')
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record && $record->tabung) {
                            // Parse the JSON from tabung column
                            $tabungData = is_string($record->tabung) ? json_decode($record->tabung, true) : $record->tabung;
                            
                            if (is_array($tabungData)) {
                                $formattedData = array_map(function ($item) {
                                    return [
                                        'qr_code' => $item['qr_code'] ?? '',
                                        'status' => (isset($item['volume']) && $item['volume'] > 0) ? 'Isi' : 'Kosong',
                                    ];
                                }, $tabungData);
                                
                                $component->state($formattedData);
                            }
                        }
                    }),

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
