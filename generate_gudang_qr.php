<?php

use App\Models\Gudang;

// Trigger QR code generation for all existing gudangs
$gudangs = Gudang::all();

foreach ($gudangs as $gudang) {
    $gudang->generateQrCode();
    echo "Triggered QR code generation for Gudang: {$gudang->kode_gudang}\n";
}

echo "Total {$gudangs->count()} gudangs processed.\n";
