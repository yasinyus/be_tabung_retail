<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for each resource
        $permissions = [
            // Dashboard
            'view_dashboard',
            
            // Users (admin only)
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Tabung (accessible by admin_umum)
            'view_tabung',
            'create_tabung',
            'edit_tabung',
            'delete_tabung',
            
            // Volume Tabung / Gas (accessible by admin_umum)
            'view_volume_tabung',
            'create_volume_tabung',
            'edit_volume_tabung',
            'delete_volume_tabung',
            
            // Armada (accessible by admin_umum)
            'view_armada',
            'create_armada',
            'edit_armada',
            'delete_armada',
            
            // Pelanggan (accessible by admin_umum)
            'view_pelanggan',
            'create_pelanggan',
            'edit_pelanggan',
            'delete_pelanggan',
            
            // Gudang (accessible by admin_umum)
            'view_gudang',
            'create_gudang',
            'edit_gudang',
            'delete_gudang',
            
            // Tabung Activity (accessible by admin_umum)
            'view_tabung_activity',
            'create_tabung_activity',
            'edit_tabung_activity',
            'delete_tabung_activity',
            
            // Deposits (admin only)
            'view_deposits',
            'create_deposits',
            'edit_deposits',
            'delete_deposits',
            
            // Audit (admin only)
            'view_audit',
            'create_audit',
            'edit_audit',
            'delete_audit',
            
            // Transactions (admin only)
            'view_transactions',
            'create_transactions',
            'edit_transactions',
            'delete_transactions',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminUmumRole = Role::firstOrCreate(['name' => 'admin_umum']);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign limited permissions to admin_umum
        $adminUmumPermissions = [
            'view_dashboard',
            'view_tabung', 'create_tabung', 'edit_tabung', 'delete_tabung',
            'view_volume_tabung', 'create_volume_tabung', 'edit_volume_tabung', 'delete_volume_tabung',
            'view_armada', 'create_armada', 'edit_armada', 'delete_armada',
            'view_pelanggan', 'create_pelanggan', 'edit_pelanggan', 'delete_pelanggan',
            'view_gudang', 'create_gudang', 'edit_gudang', 'delete_gudang',
            'view_tabung_activity', 'create_tabung_activity', 'edit_tabung_activity', 'delete_tabung_activity',
        ];

        $adminUmumRole->givePermissionTo($adminUmumPermissions);

        $this->command->info('Roles and permissions created successfully!');
    }
}
