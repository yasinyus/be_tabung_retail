<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

// Create a test user with admin_umum role
$user = User::create([
    'name' => 'Admin Umum Test',
    'email' => 'admin_umum@test.com',
    'password' => bcrypt('password123'),
]);

$adminUmumRole = Role::where('name', 'admin_umum')->first();
$user->assignRole($adminUmumRole);

echo "Test user created with admin_umum role\n";

// Test permissions
echo "Testing permissions for admin_umum user:\n";
echo "Can view tabung: " . ($user->can('view_tabung') ? 'YES' : 'NO') . "\n";
echo "Can view pelanggan: " . ($user->can('view_pelanggan') ? 'YES' : 'NO') . "\n";
echo "Can view armada: " . ($user->can('view_armada') ? 'YES' : 'NO') . "\n";
echo "Can view gudang: " . ($user->can('view_gudang') ? 'YES' : 'NO') . "\n";
echo "Can view tabung_activity: " . ($user->can('view_tabung_activity') ? 'YES' : 'NO') . "\n";
echo "Can view volume_tabung: " . ($user->can('view_volume_tabung') ? 'YES' : 'NO') . "\n";

echo "\nTesting DENIED permissions for admin_umum user:\n";
echo "Can view user: " . ($user->can('view_user') ? 'YES' : 'NO') . "\n";
echo "Can view deposit: " . ($user->can('view_deposit') ? 'YES' : 'NO') . "\n";
echo "Can view audit: " . ($user->can('view_audit') ? 'YES' : 'NO') . "\n";
echo "Can view transaction: " . ($user->can('view_transaction') ? 'YES' : 'NO') . "\n";

// Test role-based access
echo "\nRole-based access test:\n";
echo "Has admin role: " . ($user->hasRole('admin') ? 'YES' : 'NO') . "\n";
echo "Has admin_umum role: " . ($user->hasRole('admin_umum') ? 'YES' : 'NO') . "\n";

// Clean up
$user->delete();
echo "\nTest user cleaned up.\n";
