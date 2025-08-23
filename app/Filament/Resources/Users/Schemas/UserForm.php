<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                    
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin_utama' => 'Administrator Utama',
                        'admin_umum' => 'Administrator Umum',
                        'kepala_gudang' => 'Kepala Gudang',
                        'operator_retail' => 'Operator Retail',
                        'driver' => 'Driver',
                        'auditor' => 'Auditor',
                        'pelanggan_umum' => 'Pelanggan Umum',
                        'pelanggan_agen' => 'Pelanggan Agen',
                    ])
                    ->required()
                    ->default('pelanggan_umum'),
                    
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(6)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password.'),
            ]);
    }
}
