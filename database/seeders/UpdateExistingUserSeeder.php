<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateExistingUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update user admin@gmail.com to have admin_utama role
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        
        if ($adminUser) {
            $adminUser->update(['role' => 'admin_utama']);
            $adminUser->assignRole('admin_utama');
            $this->command->info('User admin@gmail.com updated with admin_utama role');
        } else {
            $this->command->warn('User admin@gmail.com not found');
        }
    }
}
