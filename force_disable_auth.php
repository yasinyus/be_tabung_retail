<?php

echo "=== FORCE DISABLE AUTHENTICATION (Server Fix) ===\n";

// 1. Backup current AdminPanelProvider
$providerPath = 'app/Providers/Filament/AdminPanelProvider.php';
$backupPath = 'app/Providers/Filament/AdminPanelProvider.php.backup';

if (!file_exists($backupPath)) {
    copy($providerPath, $backupPath);
    echo "✅ Created backup\n";
}

// 2. Create new AdminPanelProvider without auth
$newContent = '<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
            // AUTHENTICATION DISABLED FOR DEBUGGING
            ->colors([
                \'primary\' => Color::Amber,
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
echo "✅ Created no-auth AdminPanelProvider\n";

// 3. Clear cache
system('php artisan config:clear');
system('php artisan route:clear');
echo "✅ Cache cleared\n";

echo "\n🌐 Admin panel should now be accessible without login\n";
echo "🔗 URL: http://your-domain/admin\n";
echo "⚠️  REMEMBER: This disables ALL authentication!\n";
echo "🔧 To restore later: php restore_auth.php\n";

?>
