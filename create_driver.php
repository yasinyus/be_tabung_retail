<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

try {
    // Check if driver user already exists
    $existingDriver = User::where('email', 'driver@mobile.test')->first();
    
    if ($existingDriver) {
        echo "Driver user already exists: " . $existingDriver->name . "\n";
        echo "Roles: " . $existingDriver->getRoleNames()->implode(', ') . "\n";
    } else {
        // Create driver user
        $driver = User::create([
            'name' => 'Driver Mobile',
            'email' => 'driver@mobile.test',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Assign driver role
        $driver->assignRole('driver');

        echo "Driver user created successfully!\n";
        echo "Name: " . $driver->name . "\n";
        echo "Email: " . $driver->email . "\n";
        echo "Roles: " . $driver->getRoleNames()->implode(', ') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
