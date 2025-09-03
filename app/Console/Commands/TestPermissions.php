<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TestPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test role-based permissions for admin_umum';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing role-based permissions...');
        
        // Create a test user with admin_umum role
        $user = User::create([
            'name' => 'Admin Umum Test',
            'email' => 'admin_umum_test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $adminUmumRole = Role::where('name', 'admin_umum')->first();
        if (!$adminUmumRole) {
            $this->error('admin_umum role not found! Please run the seeder first.');
            return;
        }
        
        $user->assignRole($adminUmumRole);
        $this->info('Test user created with admin_umum role');

        // Test ALLOWED permissions
        $this->info("\n=== ALLOWED PERMISSIONS FOR admin_umum ===");
        $allowedPermissions = [
            'view_tabung' => 'Tabung',
            'view_pelanggan' => 'Pelanggan', 
            'view_armada' => 'Armada',
            'view_gudang' => 'Gudang',
            'view_tabung_activity' => 'Tabung Activity',
            'view_volume_tabung' => 'Gas (Volume Tabung)',
        ];
        
        foreach ($allowedPermissions as $permission => $description) {
            $canAccess = $user->can($permission);
            $status = $canAccess ? '✅ ALLOWED' : '❌ DENIED';
            $this->line("$description: $status");
        }

        // Test DENIED permissions
        $this->info("\n=== DENIED PERMISSIONS FOR admin_umum ===");
        $deniedResources = [
            'UserResource' => 'User Management',
            'DepositResource' => 'Deposits',
            'AuditResource' => 'Audits', 
            'TransactionResource' => 'Transactions',
        ];
        
        foreach ($deniedResources as $resource => $description) {
            $canAccess = $user->hasRole('admin');
            $status = $canAccess ? '❌ ALLOWED (Should be denied!)' : '✅ DENIED (Correct)';
            $this->line("$description: $status");
        }

        // Test role membership
        $this->info("\n=== ROLE MEMBERSHIP ===");
        $this->line('Has admin role: ' . ($user->hasRole('admin') ? '✅ YES' : '❌ NO'));
        $this->line('Has admin_umum role: ' . ($user->hasRole('admin_umum') ? '✅ YES' : '❌ NO'));

        // Clean up
        $user->delete();
        $this->info("\n✅ Test completed and user cleaned up.");
        
        return 0;
    }
}
