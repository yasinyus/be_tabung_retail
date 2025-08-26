<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING FILAMENT RESOURCES ===\n\n";

$resources = [
    'UserResource' => \App\Filament\Resources\UserResource::class,
    'TabungResource' => \App\Filament\Resources\Tabungs\TabungResource::class,
    'ArmadaResource' => \App\Filament\Resources\Armadas\ArmadaResource::class,
    'PelangganResource' => \App\Filament\Resources\Pelanggans\PelangganResource::class,
    'GudangResource' => \App\Filament\Resources\Gudangs\GudangResource::class,
];

foreach ($resources as $name => $class) {
    echo "🔍 Checking {$name}:\n";
    
    try {
        // Check authorization methods
        $canViewAny = method_exists($class, 'canViewAny') ? ($class::canViewAny() ? '✅' : '❌') : '⚠️ Missing';
        $canCreate = method_exists($class, 'canCreate') ? ($class::canCreate() ? '✅' : '❌') : '⚠️ Missing';
        $canEdit = method_exists($class, 'canEdit') ? '✅ Available' : '⚠️ Missing';
        $canDelete = method_exists($class, 'canDelete') ? '✅ Available' : '⚠️ Missing';
        
        echo "  - canViewAny(): {$canViewAny}\n";
        echo "  - canCreate(): {$canCreate}\n";
        echo "  - canEdit(): {$canEdit}\n";
        echo "  - canDelete(): {$canDelete}\n";
        
        // Check pages
        $pages = $class::getPages();
        echo "  - Pages:\n";
        foreach ($pages as $pageName => $pageClass) {
            echo "    ✅ {$pageName}: " . get_class($pageClass) . "\n";
        }
        
    } catch (Exception $e) {
        echo "  ❌ Error: {$e->getMessage()}\n";
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "📊 Dashboard: Available\n";
echo "👥 User Management: " . (class_exists(\App\Filament\Resources\UserResource::class) ? '✅' : '❌') . "\n";
echo "🔥 Tabung Gas: " . (class_exists(\App\Filament\Resources\Tabungs\TabungResource::class) ? '✅' : '❌') . "\n";
echo "🚛 Armada Kendaraan: " . (class_exists(\App\Filament\Resources\Armadas\ArmadaResource::class) ? '✅' : '❌') . "\n";
echo "👤 Pelanggan: " . (class_exists(\App\Filament\Resources\Pelanggans\PelangganResource::class) ? '✅' : '❌') . "\n";
echo "🏠 Gudang: " . (class_exists(\App\Filament\Resources\Gudangs\GudangResource::class) ? '✅' : '❌') . "\n";

echo "\n🎯 Expected Buttons:\n";
echo "- Header: Create button (top right)\n";
echo "- Row Actions: View, Edit, QR Code buttons\n";
echo "- Bulk Actions: Delete selected\n";

?>
