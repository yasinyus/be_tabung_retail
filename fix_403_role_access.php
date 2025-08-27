<?php

echo "=== FIX 403 - ALLOW ALL ROLES ACCESS ADMIN ===\n\n";

// Bootstrap Laravel to check current users
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "✅ Laravel loaded successfully\n\n";
    
    // Check current users and their roles
    echo "1️⃣  Current Users in Database:\n";
    $users = \App\Models\User::all();
    
    foreach ($users as $user) {
        $canAccess = $user->canAccessPanel(null);
        $status = $canAccess ? '✅ CAN ACCESS' : '❌ BLOCKED';
        echo "   {$status} - {$user->name} ({$user->email}) [{$user->role}]\n";
    }
    
    if ($users->where('canAccessPanel', false)->count() > 0) {
        echo "\n⚠️  PROBLEM: Some users cannot access admin panel!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel error: {$e->getMessage()}\n";
}

// Create super permissive User model for server
echo "\n2️⃣  Creating Super Permissive User Model...\n";

$superPermissiveUser = '<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $fillable = [
        \'name\',
        \'email\',
        \'password\',
        \'role\',
    ];

    protected $hidden = [
        \'password\',
        \'remember_token\',
    ];

    protected function casts(): array
    {
        return [
            \'email_verified_at\' => \'datetime\',
            \'password\' => \'hashed\',
        ];
    }

    /**
     * SUPER PERMISSIVE: Allow ALL users to access admin panel
     * This is for debugging 403 issues - ALL ROLES ALLOWED
     */
    public function canAccessPanel($panel): bool
    {
        // DEBUGGING: Allow absolutely everyone
        return true;
        
        /* Original role-based access (commented out for debugging):
        $allowedRoles = [
            \'admin_utama\',
            \'admin_umum\', 
            \'kepala_gudang\',
            \'operator_retail\',
            \'driver\'
        ];
        return in_array($this->role, $allowedRoles);
        */
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, [\'admin_utama\', \'admin_umum\']);
    }
    
    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === \'admin_utama\';
    }
}';

file_put_contents('super_permissive_User.php', $superPermissiveUser);
echo "   ✅ super_permissive_User.php created\n";

// Create deployment script
echo "\n3️⃣  Creating Server Deployment Script...\n";

$deployScript = '#!/bin/bash
echo "=== DEPLOYING SUPER PERMISSIVE USER MODEL ==="

echo "1. Backing up existing User model..."
cp app/Models/User.php app/Models/User.php.backup

echo "2. Deploying super permissive User model..."
cp super_permissive_User.php app/Models/User.php

echo "3. Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan optimize:clear

echo "4. Testing user access..."
php -r "
require_once \'vendor/autoload.php\';
\$app = require_once \'bootstrap/app.php\';
\$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo \"Testing user access:\n\";
\$users = \App\Models\User::limit(3)->get();
foreach (\$users as \$user) {
    \$canAccess = \$user->canAccessPanel(null) ? \'YES\' : \'NO\';
    echo \"- {\$user->name} ({\$user->role}): {\$canAccess}\n\";
}
"

echo "✅ SUPER PERMISSIVE MODE ACTIVATED!"
echo "ALL USERS CAN NOW ACCESS ADMIN PANEL"
echo "Test: http://your-domain/admin"
';

file_put_contents('deploy_super_permissive.sh', $deployScript);
chmod('deploy_super_permissive.sh', 0755);
echo "   ✅ deploy_super_permissive.sh created\n";

echo "\n🚀 DEPLOYMENT INSTRUCTIONS:\n";
echo "1. Upload to server:\n";
echo "   - super_permissive_User.php\n";
echo "   - deploy_super_permissive.sh\n";

echo "\n2. Run on server:\n";
echo "   chmod +x deploy_super_permissive.sh\n";
echo "   ./deploy_super_permissive.sh\n";

echo "\n3. Test admin access:\n";
echo "   http://8.215.70.68/admin\n";

echo "\n🎯 EXPECTED RESULT:\n";
echo "✅ Login page appears (not 403)\n";
echo "✅ Any user can login successfully\n";
echo "✅ Dashboard loads without errors\n";

echo "\n⚠️  SECURITY NOTE:\n";
echo "This allows ALL users to access admin panel!\n";
echo "After confirming it works, restore proper role-based access.\n";

?>
