<?php

namespace App\Filament\Resources\Pelanggans;

use App\Filament\Resources\Pelanggans\Pages\CreatePelanggan;
use App\Filament\Resources\Pelanggans\Pages\EditPelanggan;
use App\Filament\Resources\Pelanggans\Pages\ListPelanggans;
use App\Filament\Resources\Pelanggans\Schemas\PelangganForm;
use App\Filament\Resources\Pelanggans\Tables\PelanggansTable;
use App\Models\Pelanggan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $recordTitleAttribute = 'nama_pelanggan';

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return in_array($user->role, ['admin_utama', 'admin_umum', 'keuangan']);
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return in_array($user->role, ['admin_utama', 'admin_umum', 'keuangan']);
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return in_array($user->role, ['admin_utama', 'admin_umum', 'keuangan']);
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return in_array($user->role, ['admin_utama', 'admin_umum', 'keuangan']);
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return in_array($user->role, ['admin_utama', 'admin_umum', 'keuangan']);
    }

    public static function canDeleteAny(): bool
    {
        return true; // Temporarily disable authorization for debugging
    }

    public static function form(Schema $schema): Schema
    {
        return PelangganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PelanggansTable::configure($table);
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
            'index' => ListPelanggans::route('/'),
            'create' => CreatePelanggan::route('/create'),
            'edit' => EditPelanggan::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return in_array($user->role, ['admin_utama', 'admin_umum', 'keuangan']);
    }
}
