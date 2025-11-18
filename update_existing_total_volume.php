<?php

// Script untuk mengupdate total_volume di data aktivitas tabung yang sudah ada
// Jalankan dengan: php update_existing_total_volume.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TabungActivity;
use App\Models\StokTabung;

echo "Mulai update total_volume untuk aktivitas tabung yang sudah ada...\n\n";

// Ambil semua aktivitas yang total_volume nya masih null atau 0
$activities = TabungActivity::whereNull('total_volume')
    ->orWhere('total_volume', 0)
    ->get();

$updated = 0;
$failed = 0;

foreach ($activities as $activity) {
    try {
        $totalVolume = 0;
        
        // Hitung total volume dari tabung
        if ($activity->tabung && is_array($activity->tabung)) {
            foreach ($activity->tabung as $item) {
                $kodeTabung = is_array($item) && isset($item['qr_code']) ? $item['qr_code'] : $item;
                
                if ($kodeTabung) {
                    $stokTabung = StokTabung::where('kode_tabung', $kodeTabung)->first();
                    if ($stokTabung && $stokTabung->volume) {
                        $totalVolume += $stokTabung->volume;
                    }
                }
            }
        }
        
        // Update total_volume
        $activity->total_volume = $totalVolume;
        $activity->save();
        
        $updated++;
        echo "✓ Updated ID {$activity->id}: {$activity->nama_aktivitas} - Volume: {$totalVolume} m³\n";
        
    } catch (\Exception $e) {
        $failed++;
        echo "✗ Failed ID {$activity->id}: {$e->getMessage()}\n";
    }
}

echo "\n=== SELESAI ===\n";
echo "Total diupdate: {$updated}\n";
echo "Total gagal: {$failed}\n";
echo "Total aktivitas: " . ($updated + $failed) . "\n";
