<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\LaporanPelanggan;

// Update existing laporan with sample list_tabung data
$laporans = LaporanPelanggan::where('kode_pelanggan', 'PU002')->get();

foreach($laporans as $laporan) {
    $sampleListTabung = [
        [
            'kode_tabung' => 'TB001-' . rand(100, 999),
            'volume' => '3kg',
            'jenis' => 'Gas LPG',
            'harga' => 50000,
            'brand' => 'Pertamina'
        ],
        [
            'kode_tabung' => 'TB002-' . rand(100, 999),
            'volume' => '12kg',
            'jenis' => 'Gas LPG',
            'harga' => 150000,
            'brand' => 'Pertamina'
        ]
    ];
    
    $laporan->update(['list_tabung' => $sampleListTabung]);
    echo "Updated laporan ID {$laporan->id} with list_tabung\n";
}

echo "All laporan updated with sample list_tabung data!\n";
