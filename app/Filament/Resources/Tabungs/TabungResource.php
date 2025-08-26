<?php

namespace App\Filament\Resources\Tabungs;

use App\Filament\Resources\Tabungs\Pages\CreateTabung;
use App\Filament\Resources\Tabungs\Pages\EditTabung;
use App\Filament\Resources\Tabungs\Pages\ListTabungs;
use App\Filament\Resources\Tabungs\Schemas\TabungForm;
use App\Filament\Resources\Tabungs\Tables\TabungsTable;
use App\Models\Tabung;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TabungResource extends Resource
{
    protected static ?string $model = Tabung::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Tabung Gas';

    protected static ?string $recordTitleAttribute = 'kode_tabung';

    // Menggunakan policy untuk mengatur akses
    public static function canViewAny(): bool
    {
        return true; // Temporarily disable authorization for debugging
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang', 'operator_retail']);
    }

    public static function form(Schema $schema): Schema
    {
        return TabungForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TabungsTable::configure($table);
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
            'index' => ListTabungs::route('/'),
            'create' => CreateTabung::route('/create'),
            'edit' => EditTabung::route('/{record}/edit'),
        ];
    }
}
