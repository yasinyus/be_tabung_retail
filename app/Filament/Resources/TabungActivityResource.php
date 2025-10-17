<?php

namespace App\Filament\Resources;

use App\Models\TabungActivity;
use App\Models\User;
use App\Filament\Resources\TabungActivityResource\Pages;
use App\Filament\Resources\TabungActivityResource\Schemas\TabungActivityForm;
use App\Filament\Resources\TabungActivityResource\Tables\TabungActivitiesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TabungActivityResource extends Resource
{
    protected static ?string $model = TabungActivity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Aktivitas Tabung';

    protected static ?string $modelLabel = 'Aktivitas Tabung';

    protected static ?string $pluralModelLabel = 'Aktivitas Tabung';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'nama_aktivitas';

    public static function form(Schema $schema): Schema
    {
        return TabungActivityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TabungActivitiesTable::configure($table);
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
            'index' => Pages\ListTabungActivities::route('/'),
            'create' => Pages\CreateTabungActivity::route('/create'),
            'view' => Pages\ViewTabungActivity::route('/{record}'),
            'edit' => Pages\EditTabungActivity::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->role === 'admin_utama' 
            || $user->hasRole('admin_utama')
            || $user->role === 'admin_umum'
            || $user->hasRole('admin_umum')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->role === 'admin_utama' 
            || $user->hasRole('admin_utama')
            || $user->role === 'admin_umum'
            || $user->hasRole('admin_umum')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
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
            || $user->role === 'admin_umum'
            || $user->hasRole('admin_umum')
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
            || $user->role === 'admin_umum'
            || $user->hasRole('admin_umum')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
    }

    public static function canView($record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->role === 'admin_utama' 
            || $user->hasRole('admin_utama')
            || $user->role === 'admin_umum'
            || $user->hasRole('admin_umum')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
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
            || $user->role === 'admin_umum'
            || $user->hasRole('admin_umum')
            || $user->role === 'keuangan'
            || $user->hasRole('keuangan');
    }
}
