<?php

namespace App\Filament\Resources\Gudangs;

use App\Filament\Resources\Gudangs\Pages\CreateGudang;
use App\Filament\Resources\Gudangs\Pages\EditGudang;
use App\Filament\Resources\Gudangs\Pages\ListGudangs;
use App\Filament\Resources\Gudangs\Schemas\GudangForm;
use App\Filament\Resources\Gudangs\Tables\GudangsTable;
use App\Models\Gudang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GudangResource extends Resource
{
    protected static ?string $model = Gudang::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Gudang';

    protected static ?string $recordTitleAttribute = 'nama_gudang';

    public static function canViewAny(): bool
    {
        return true; // Temporarily disable authorization for debugging
    }

    public static function canView($record): bool
    {
        return true; // Temporarily disable authorization for debugging
    }

    public static function canCreate(): bool
    {
        return true; // Temporarily disable authorization for debugging
    }

    public static function canEdit($record): bool
    {
        return true; // Temporarily disable authorization for debugging
    }

    public static function canDelete($record): bool
    {
        return true; // Temporarily disable authorization for debugging
    }

    public static function canDeleteAny(): bool
    {
        return true; // Temporarily disable authorization for debugging
    }

    public static function form(Schema $schema): Schema
    {
        return GudangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GudangsTable::configure($table);
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
            'index' => ListGudangs::route('/'),
            'create' => CreateGudang::route('/create'),
            'edit' => EditGudang::route('/{record}/edit'),
        ];
    }
}
