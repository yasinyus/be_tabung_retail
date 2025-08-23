<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$driver = User::where('email', 'driver@mobile.test')->first();
if ($driver) {
    // Update password
    $driver->password = Hash::make('password123');
    $driver->save();
    
    echo 'Driver password updated successfully!' . PHP_EOL;
    echo 'New password check: ' . (Hash::check('password123', $driver->password) ? 'PASS' : 'FAIL') . PHP_EOL;
} else {
    echo 'Driver not found!' . PHP_EOL;
}
