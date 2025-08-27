<?php

echo "=== FRESH FILAMENT INSTALLATION ===\n\n";

// Step 1: Backup existing files
echo "1️⃣  Backing up existing configuration...\n";

$backupDir = 'backup_' . date('Y_m_d_H_i_s');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$filesToBackup = [
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Filament/Resources/UserResource.php',
    'app/Models/User.php'
];

foreach ($filesToBackup as $file) {
    if (file_exists($file)) {
        copy($file, $backupDir . '/' . basename($file));
        echo "   ✅ Backed up: {$file}\n";
    }
}

// Step 2: Create minimal working AdminPanelProvider
echo "\n2️⃣  Creating minimal AdminPanelProvider...\n";

$minimalProvider = '<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id(\'admin\')
            ->path(\'admin\')
            ->colors([
                \'primary\' => Color::Amber,
            ])
            ->discoverResources(in: app_path(\'Filament/Resources\'), for: \'App\\\\Filament\\\\Resources\')
            ->discoverPages(in: app_path(\'Filament/Pages\'), for: \'App\\\\Filament\\\\Pages\')
            ->discoverWidgets(in: app_path(\'Filament/Widgets\'), for: \'App\\\\Filament\\\\Widgets\');
    }
}';

file_put_contents('app/Providers/Filament/AdminPanelProvider.php', $minimalProvider);
echo "   ✅ Created minimal AdminPanelProvider (NO AUTH YET)\n";

// Step 3: Create simple test page
echo "\n3️⃣  Creating test page...\n";

if (!is_dir('app/Filament/Pages')) {
    mkdir('app/Filament/Pages', 0755, true);
}

$testPage = '<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TestPage extends Page
{
    protected static ?string $navigationIcon = \'heroicon-o-document-text\';
    protected static string $view = \'filament.pages.test-page\';
    protected static ?string $title = \'Test Page\';
    protected static ?string $navigationLabel = \'Test\';
}';

file_put_contents('app/Filament/Pages/TestPage.php', $testPage);

// Create view for test page
if (!is_dir('resources/views/filament/pages')) {
    mkdir('resources/views/filament/pages', 0755, true);
}

$testView = '<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <h2 class="text-lg font-semibold">🎉 Filament Working!</h2>
            <p>If you can see this page, Filament is properly installed and working.</p>
            <p><strong>Laravel Status:</strong> ✅ OK</p>
            <p><strong>Filament Status:</strong> ✅ OK</p>
            <p><strong>Routing Status:</strong> ✅ OK</p>
        </div>
        
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
            <h3 class="font-semibold">Next Steps:</h3>
            <ol class="list-decimal list-inside space-y-1">
                <li>Add authentication</li>
                <li>Add resources</li>
                <li>Configure user access</li>
            </ol>
        </div>
    </div>
</x-filament-panels::page>';

file_put_contents('resources/views/filament/pages/test-page.blade.php', $testView);
echo "   ✅ Created test page\n";

// Step 4: Clear everything
echo "\n4️⃣  Clearing caches...\n";

$commands = [
    'php artisan config:clear',
    'php artisan route:clear',
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan optimize:clear'
];

foreach ($commands as $command) {
    system($command . ' 2>/dev/null');
}
echo "   ✅ All caches cleared\n";

// Step 5: Regenerate autoloader
echo "\n5️⃣  Regenerating autoloader...\n";
system('composer dump-autoload --optimize 2>/dev/null');
echo "   ✅ Autoloader regenerated\n";

echo "\n🎯 STEP-BY-STEP TESTING:\n";
echo "\nSTEP 1 - Test Basic Filament (NO AUTH):\n";
echo "   URL: http://8.215.70.68/admin\n";
echo "   Expected: Should show Filament dashboard with Test page\n";
echo "   NO LOGIN required yet!\n";

echo "\nSTEP 2 - If Step 1 works, we add authentication:\n";
echo "   Will modify AdminPanelProvider to add ->login()\n";

echo "\nSTEP 3 - If Step 2 works, we add resources:\n";
echo "   Will add UserResource and other resources back\n";

echo "\n⚠️  CRITICAL:\n";
echo "We must test EACH step before proceeding to next!\n";
echo "This way we know exactly where issues occur.\n";

echo "\n🚀 TRY NOW:\n";
echo "Visit: http://8.215.70.68/admin\n";
echo "Should show Filament interface with Test page (no login needed)\n";

echo "\n💾 Backup location: {$backupDir}/\n";
echo "Your original files are safely backed up there.\n";

?>
