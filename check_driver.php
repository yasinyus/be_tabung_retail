<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$driver = User::where('email', 'driver@mobile.test')->first();
if ($driver) {
    echo 'Driver found: ' . $driver->name . PHP_EOL;
    echo 'Has driver role: ' . ($driver->hasRole('driver') ? 'YES' : 'NO') . PHP_EOL;
    echo 'All roles: ' . $driver->getRoleNames()->implode(', ') . PHP_EOL;
    echo 'Password check test: ' . (Hash::check('password123', $driver->password) ? 'PASS' : 'FAIL') . PHP_EOL;
} else {
    echo 'Driver not found!' . PHP_EOL;
}
