<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\VolumeTabung;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing data structure from status to volume
        $records = VolumeTabung::all();
        
        foreach ($records as $record) {
            $tabungData = $record->tabung;
            if (is_array($tabungData)) {
                $updatedTabung = array_map(function ($item) {
                    // Convert status to volume
                    if (isset($item['status'])) {
                        $volume = $item['status'] === 'Isi' ? 12.5 : 0; // Default volume for Isi = 12.5L, Kosong = 0L
                        unset($item['status']);
                        $item['volume'] = $volume;
                    }
                    return $item;
                }, $tabungData);
                
                $record->tabung = $updatedTabung;
                $record->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert volume back to status
        $records = VolumeTabung::all();
        
        foreach ($records as $record) {
            $tabungData = $record->tabung;
            if (is_array($tabungData)) {
                $revertedTabung = array_map(function ($item) {
                    // Convert volume back to status
                    if (isset($item['volume'])) {
                        $status = $item['volume'] > 0 ? 'Isi' : 'Kosong';
                        unset($item['volume']);
                        $item['status'] = $status;
                    }
                    return $item;
                }, $tabungData);
                
                $record->tabung = $revertedTabung;
                $record->save();
            }
        }
    }
};
