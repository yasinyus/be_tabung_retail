<?php

namespace App\Filament\Resources\RefundTabungs;

use App\Filament\Resources\RefundTabungs\Pages\CreateRefundTabung;
use App\Filament\Resources\RefundTabungs\Pages\EditRefundTabung;
use App\Filament\Resources\RefundTabungs\Pages\ListRefundTabungs;
use App\Filament\Resources\RefundTabungs\Pages\ViewRefundTabung;
use App\Filament\Resources\RefundTabungs\Schemas\RefundTabungForm;
use App\Filament\Resources\RefundTabungs\Tables\RefundTabungsTable;
use App\Models\SerahTerimaTabung;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RefundTabungResource extends Resource
{
    protected static ?string $model = SerahTerimaTabung::class;

    protected static ?string $slug = 'refund-tabungs';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Refund Tabung';

    protected static ?string $modelLabel = 'Refund Tabung';

    protected static ?string $pluralModelLabel = 'Refund Tabung';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'bast_id';

    public static function form(Schema $schema): Schema
    {
        // Check if we're in view mode based on the current route
        $currentRoute = request()->route()->getName();
        $isViewMode = $currentRoute && str_contains($currentRoute, '.view');
        
        if ($isViewMode) {
            return \App\Filament\Resources\RefundTabungs\Schemas\RefundTabungViewForm::configure($schema);
        }
        
        return RefundTabungForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RefundTabungsTable::configure($table);
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
            'index' => ListRefundTabungs::route('/'),
            'create' => CreateRefundTabung::route('/create'),
            'view' => ViewRefundTabung::route('/{record}'),
            'edit' => EditRefundTabung::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide this menu from navigation
    }

    public static function canViewAny(): bool
    {
        return true;
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang']);
    }

    public static function canView($record): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama', 'admin_umum']);
    }

    public static function canEdit($record): bool
    {
        return true;
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama', 'admin_umum']);
    }

    public static function canDelete($record): bool
    {
        return true;
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['admin_utama']);
    }
}