<?php

namespace App\Filament\Resources\UserResource\Schemas;

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
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                    
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin_utama' => 'Admin Utama',
                        'admin_umum' => 'Admin Umum',
                        'kepala_gudang' => 'Kepala Gudang',
                        'operator_retail' => 'Operator Retail',
                        'driver' => 'Driver',
                    ])
                    ->required()
                    ->native(false),
                    
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn ($context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255)
                    ->helperText('Kosongkan jika tidak ingin mengubah password'),
            ]);
    }
}
