<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CREATE SECURE ADMIN USER ===\n\n";

try {
    // Create atau update admin user
    $adminData = [
        'name' => 'Super Admin',
        'email' => 'admin@ptgas.com',
        'role' => 'admin_utama',
        'email_verified_at' => now(),
        'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
    ];
    
    $admin = \App\Models\User::updateOrCreate(
        ['email' => 'admin@ptgas.com'],
        $adminData
    );
    
    echo "✅ Admin user created/updated:\n";
    echo "   Name: {$admin->name}\n";
    echo "   Email: {$admin->email}\n";
    echo "   Role: {$admin->role}\n";
    echo "   Password: admin123\n";
    
    // Test canAccessPanel
    if (method_exists($admin, 'canAccessPanel')) {
        $canAccess = $admin->canAccessPanel(null);
        echo "   Can Access Panel: " . ($canAccess ? 'YES' : 'NO') . "\n";
    }
    
    // Verify user exists
    $verification = \App\Models\User::where('email', 'admin@ptgas.com')->first();
    if ($verification) {
        echo "✅ User verification: SUCCESS\n";
    } else {
        echo "❌ User verification: FAILED\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error creating admin: {$e->getMessage()}\n";
}

echo "\n🔑 LOGIN CREDENTIALS:\n";
echo "URL: http://your-domain/admin/login\n";
echo "Email: admin@ptgas.com\n";
echo "Password: admin123\n";

echo "\n🛡️  SECURITY NOTES:\n";
echo "1. Change password after first login\n";
echo "2. Use strong password in production\n";
echo "3. Enable 2FA if available\n";
echo "4. Regular security audits\n";

?>
