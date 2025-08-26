<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ENABLE AUTH WITH PROPER USER SETUP ===\n";

// 1. Ensure users exist
echo "1️⃣  Checking users...\n";
$userCount = \App\Models\User::count();
echo "   Users in database: {$userCount}\n";

if ($userCount == 0) {
    echo "   Creating admin user...\n";
    \App\Models\User::create([
        'name' => 'Super Admin',
        'email' => 'admin@ptgas.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'role' => 'admin_utama',
        'email_verified_at' => now(),
    ]);
    echo "   ✅ Admin user created\n";
} else {
    echo "   ✅ Users already exist\n";
}

// 2. Restore proper AdminPanelProvider with auth
echo "2️⃣  Restoring authentication...\n";

$providerPath = 'app/Providers/Filament/AdminPanelProvider.php';
$properContent = '<?php

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
            ->login()
            ->authGuard(\'web\')
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
                Authenticate::class,
            ]);
    }
}';

file_put_contents($providerPath, $properContent);
echo "   ✅ Authentication enabled\n";

// 3. Clear cache
echo "3️⃣  Clearing cache...\n";
system('php artisan config:clear');
system('php artisan route:clear');
system('php artisan cache:clear');
echo "   ✅ Cache cleared\n";

echo "\n🎉 SETUP COMPLETED!\n";
echo "🔐 Authentication is now properly enabled\n";
echo "📧 Login with: admin@ptgas.com\n";
echo "🔑 Password: password123\n";
echo "🌐 URL: http://your-domain/admin\n";
echo "👤 User menu will appear in top right after login\n";

?>
