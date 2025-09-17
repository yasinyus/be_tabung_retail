<?php

namespace App\Filament\Widgets;

use App\Models\Gudang;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class GudangSimpleTableWidget extends Widget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static string $view = 'filament.widgets.gudang-simple-table';
    
    protected function getViewData(): array
    {
        $gudangStats = Gudang::leftJoin('stok_tabung', 'gudangs.kode_gudang', '=', 'stok_tabung.lokasi')
            ->select([
                'gudangs.nama_gudang',
                'gudangs.kode_gudang',
                DB::raw('COUNT(stok_tabung.id) as total_tabung'),
                DB::raw('COUNT(CASE WHEN stok_tabung.status = "Isi" THEN 1 END) as tabung_isi'),
                DB::raw('COUNT(CASE WHEN stok_tabung.status = "Kosong" THEN 1 END) as tabung_kosong'),
                DB::raw('SUM(CASE WHEN stok_tabung.status = "Isi" THEN stok_tabung.volume ELSE 0 END) as total_volume')
            ])
            ->groupBy('gudangs.kode_gudang', 'gudangs.nama_gudang')
            ->orderBy('gudangs.nama_gudang')
            ->get();
        
        return [
            'gudangStats' => $gudangStats
        ];
    }
}
