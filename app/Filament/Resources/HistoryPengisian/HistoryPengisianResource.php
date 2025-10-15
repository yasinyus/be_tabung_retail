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
use Illuminate\Support\Facades\Log;

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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Check both role column and Spatie roles - admin_utama dan keuangan
        return $user->role === 'admin_utama' 
            || $user->hasRole('admin_utama')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
    }

    public static function canView($record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            Log::info('HistoryPengisian canView: No user authenticated');
            return false;
        }
        
        $hasAccess = $user->role === 'admin_utama' 
            || $user->hasRole('admin_utama')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
        
        // Debugging: Log current user and role
        Log::info('HistoryPengisian canView check', [
            'user_id' => $user->id,
            'user_role_column' => $user->role,
            'user_spatie_roles' => $user->getRoleNames(),
            'record_id' => $record?->id,
            'has_access' => $hasAccess
        ]);
        
        return $hasAccess;
    }

    public static function canCreate(): bool
    {
        // Disable create functionality
        return false;
    }

    public static function canEdit($record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->role === 'admin_utama' 
            || $user->hasRole('admin_utama')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
    }

    public static function canDelete($record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->role === 'admin_utama' 
            || $user->hasRole('admin_utama')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->role === 'admin_utama' 
            || $user->hasRole('admin_utama')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
    }
}