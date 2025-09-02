<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VolumeTabungResource\Pages;
use App\Filament\Resources\VolumeTabungResource\Schemas\VolumeTabungForm;
use App\Filament\Resources\VolumeTabungResource\Tables\VolumeTabungsTable;
use App\Models\VolumeTabung;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VolumeTabungResource extends Resource
{
    protected static ?string $model = VolumeTabung::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Volume Tabung';
    
    protected static ?string $modelLabel = 'Volume Tabung';
    
    protected static ?string $pluralModelLabel = 'Volume Tabung';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return VolumeTabungForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VolumeTabungsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVolumeTabungs::route('/'),
            'create' => Pages\CreateVolumeTabung::route('/create'),
            'view' => Pages\ViewVolumeTabung::route('/{record}'),
            'edit' => Pages\EditVolumeTabung::route('/{record}/edit'),
        ];
    }
}
