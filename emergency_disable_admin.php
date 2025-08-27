<?php

// EMERGENCY: Temporarily disable admin access
$providerPath = 'app/Providers/Filament/AdminPanelProvider.php';

$emergencyProvider = '<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // EMERGENCY DISABLED - SECURITY BREACH
        throw new \Exception("Admin panel temporarily disabled for security");
        
        return $panel->default();
    }
}';

// Backup original
copy($providerPath, $providerPath . '.backup');

// Apply emergency disable
file_put_contents($providerPath, $emergencyProvider);

echo "🚨 EMERGENCY: Admin panel disabled for security!\n";
echo "Original backed up to: {$providerPath}.backup\n";
echo "This prevents unauthorized access until auth is fixed.\n";

?>
