<?php

echo "=== ADDING USER MENU TO FILAMENT PANEL ===\n";

// Backup current AdminPanelProvider
$providerPath = 'app/Providers/Filament/AdminPanelProvider.php';
$backupPath = 'app/Providers/Filament/AdminPanelProvider.php.backup2';

if (!file_exists($backupPath)) {
    copy($providerPath, $backupPath);
    echo "✅ Created backup\n";
}

// Create AdminPanelProvider with user menu
$newContent = '<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\UserMenuItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id(\'admin\')
            ->path(\'admin\')
            // AUTHENTICATION DISABLED BUT USER MENU ADDED
            ->colors([
                \'primary\' => Color::Amber,
            ])
            ->userMenuItems([
                UserMenuItem::make()
                    ->label(\'Dashboard\')
                    ->url(\'/admin\')
                    ->icon(\'heroicon-o-home\'),
                UserMenuItem::make()
                    ->label(\'Settings\')
                    ->url(\'#\')
                    ->icon(\'heroicon-o-cog-6-tooth\'),
                \'logout\' => UserMenuItem::make()
                    ->label(\'Logout\')
                    ->url(\'/admin/logout\')
                    ->icon(\'heroicon-o-arrow-right-on-rectangle\'),
            ])
            ->resources([
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\Tabungs\TabungResource::class,
                \App\Filament\Resources\Armadas\ArmadaResource::class,
                \App\Filament\Resources\Pelanggans\PelangganResource::class,
                \App\Filament\Resources\Gudangs\GudangResource::class,
            ])
            ->discoverPages(in: app_path(\'Filament/Pages\'), for: \'App\Filament\Pages\')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path(\'Filament/Widgets\'), for: \'App\Filament\Widgets\')
            ->widgets([
                // Custom widgets only
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                // NO AUTH MIDDLEWARE
            ]);
    }
}';

file_put_contents($providerPath, $newContent);
echo "✅ Added user menu to AdminPanelProvider\n";

// Clear cache
system('php artisan config:clear');
system('php artisan route:clear');
echo "✅ Cache cleared\n";

echo "\n🎯 User menu should now appear in top right corner\n";
echo "📋 Menu items: Dashboard, Settings, Logout\n";
echo "🔄 Refresh the admin panel to see changes\n";

?>
