<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Regenerating Armada QR Codes...\n";

$armadas = App\Models\Armada::all();

foreach ($armadas as $armada) {
    $qrCode = base64_encode($armada->generateQrCode());
    $armada->updateQuietly(['qr_code' => $qrCode]);
    echo "Updated QR for Armada: {$armada->nopol}\n";
}

echo "Done! Updated " . $armadas->count() . " Armada QR codes.\n";
