<?php

echo "=== FIX ADMIN PROVIDER AUTHENTICATION ===\n\n";

$providerPath = 'app/Providers/Filament/AdminPanelProvider.php';

// Backup current file first
if (file_exists($providerPath)) {
    $backup = $providerPath . '.backup.' . date('Y-m-d-H-i-s');
    copy($providerPath, $backup);
    echo "✅ Backup created: {$backup}\n";
}

// Create SECURE AdminPanelProvider with authentication
$secureProvider = '<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
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
            ->login()                    // ✅ ENABLE LOGIN
            ->authGuard(\'web\')          // ✅ SET AUTH GUARD
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
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,     // ✅ ENABLE AUTH MIDDLEWARE
            ]);
    }
}';

// Write secure provider
file_put_contents($providerPath, $secureProvider);
echo "✅ Secure AdminPanelProvider created!\n";

// Verify the fix
$content = file_get_contents($providerPath);
$checks = [
    '->login()' => strpos($content, '->login()') !== false,
    '->authGuard(' => strpos($content, '->authGuard(') !== false,
    'Authenticate::class' => strpos($content, 'Authenticate::class') !== false,
];

echo "\n🔍 Verification:\n";
foreach ($checks as $element => $exists) {
    $status = $exists ? '✅' : '❌';
    echo "   {$status} {$element}\n";
}

echo "\n🧹 Clear Caches...\n";
// Clear all caches
system('php artisan config:clear');
echo "   ✅ Config cleared\n";

system('php artisan route:clear');
echo "   ✅ Routes cleared\n";

system('php artisan cache:clear');
echo "   ✅ Cache cleared\n";

system('php artisan view:clear');
echo "   ✅ Views cleared\n";

system('php artisan optimize:clear');
echo "   ✅ Optimization cleared\n";

echo "\n🎯 AUTHENTICATION NOW ENABLED!\n";
echo "\n📝 TESTING:\n";
echo "1. Test: http://your-domain/admin\n";
echo "   Expected: Redirect to /admin/login\n";
echo "\n2. Test: http://your-domain/admin/login\n";
echo "   Expected: Login form appears\n";
echo "\n3. Login with:\n";
echo "   Email: admin@ptgas.com\n";
echo "   Password: admin123\n";

echo "\n🛡️  SECURITY STATUS: AUTHENTICATION REQUIRED\n";

?>
