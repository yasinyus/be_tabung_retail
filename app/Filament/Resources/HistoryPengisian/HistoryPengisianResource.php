<?php

namespace App\Filament\Resources\HistoryPengisian;

use App\Filament\Resources\HistoryPengisian\Pages\EditHistoryPengisian;
use App\Filament\Resources\HistoryPengisian\Pages\ListHistoryPengisians;
use App\Filament\Resources\HistoryPengisian\Pages\ViewHistoryPengisian;
use App\Filament\Resources\HistoryPengisian\Schemas\HistoryPengisianForm;
use App\Filament\Resources\HistoryPengisian\Tables\HistoryPengisiansTable;
use App\Models\VolumeTabung;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class HistoryPengisianResource extends Resource
{
    protected static ?string $model = VolumeTabung::class;

    protected static ?string $slug = 'history-pengisians';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'History Pengisian';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'id';

    // Menggunakan policy untuk mengatur akses
    public static function canViewAny(): bool
    {
        return true; // Temporarily allow all users for debugging
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang', 'operator_retail']);
    }

    public static function canView($record): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return false; // Disable create functionality
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang']);
    }

    public static function canEdit($record): bool
    {
        return true; // Temporarily allow all for debugging
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang']);
    }

    public static function canDelete($record): bool
    {
        return true; // Temporarily allow all for debugging
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama']);
    }

    public static function form(Schema $schema): Schema
    {
        return HistoryPengisianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoryPengisiansTable::configure($table);
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
            'index' => ListHistoryPengisians::route('/'),
            'view' => ViewHistoryPengisian::route('/{record}'),
            'edit' => EditHistoryPengisian::route('/{record}/edit'),
        ];
    }

     public static function shouldRegisterNavigation(): bool
    {
        return true; // Temporarily allow all users for debugging
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama']);
    }
}