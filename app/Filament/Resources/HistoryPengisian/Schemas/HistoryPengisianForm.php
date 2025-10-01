<?php

namespace App\Filament\Resources\HistoryPengisian\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class HistoryPengisianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->label('Tanggal Pengisian')
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y'),
                    
                TextInput::make('lokasi')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan lokasi pengisian'),
                    
                TextInput::make('nama')
                    ->label('Nama Petugas')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan nama petugas'),
                    
                Select::make('status')
                    ->label('Status Pengisian')
                    ->required()
                    ->options([
                        'Pending' => 'Pending',
                        'Proses' => 'Proses', 
                        'Selesai' => 'Selesai',
                        'isi' => 'Isi',
                        'kosong' => 'Kosong',
                    ])
                    ->default('Proses'),
                    
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->placeholder('Masukkan keterangan tambahan (opsional)'),
                    
                Repeater::make('tabung')
                    ->label('Daftar Tabung')
                    ->schema([
                        TextInput::make('kode_tabung')
                            ->label('Kode Tabung')
                            ->required()
                            ->placeholder('TB001'),
                            
                        TextInput::make('volume')
                            ->label('Volume (m³)')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->placeholder('0.025')
                            ->suffix('m³')
                            ->formatStateUsing(fn ($state) => $state ? number_format((float)$state, 2, '.', '') : $state)
                            ->dehydrateStateUsing(fn ($state) => $state ? round((float)$state, 2) : $state),
                    ])
                    ->collapsible()
                    ->cloneable()
                    ->deleteAction(
                        fn ($action) => $action->requiresConfirmation()
                    )
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['kode_tabung'] ?? 'Tabung Baru'),
            ]);
    }
}