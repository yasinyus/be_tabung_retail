<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating admin user...');
        
        $name = $this->ask('Enter admin name', 'Administrator Utama');
        $email = $this->ask('Enter admin email', 'admin@tabungretail.com');
        $password = $this->secret('Enter admin password');
        
        if (!$password) {
            $password = 'admin123';
            $this->info('Using default password: admin123');
        }
        
        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            $this->warn("User with email {$email} already exists!");
            
            if ($this->confirm('Do you want to update this user to admin_utama role?')) {
                $existingUser->update([
                    'name' => $name,
                    'role' => 'admin_utama',
                    'password' => Hash::make($password),
                ]);
                
                $existingUser->syncRoles([]);
                $existingUser->assignRole('admin_utama');
                
                $this->info("User {$email} has been updated with admin_utama role!");
            }
        } else {
            // Create new user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin_utama',
            ]);
            
            $user->assignRole('admin_utama');
            
            $this->info("Admin user created successfully!");
            $this->info("Email: {$email}");
            $this->info("Password: {$password}");
        }
        
        return Command::SUCCESS;
    }
}
