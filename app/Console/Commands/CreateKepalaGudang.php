<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateKepalaGudang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-kepala-gudang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create kepala_gudang user for tabung-datang API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Creating Kepala Gudang User...');

        try {
            // Check if kepala_gudang already exists
            $existingUser = User::where('email', 'kepala_gudang@example.com')->first();
            
            if ($existingUser) {
                $this->warn('⚠️ User kepala_gudang already exists:');
                $this->line("   ID: {$existingUser->id}");
                $this->line("   Name: {$existingUser->name}");
                $this->line("   Email: {$existingUser->email}");
                $this->line("   Role: {$existingUser->role}");
                
                if ($this->confirm('Do you want to update the existing user?')) {
                    $existingUser->update([
                        'name' => 'Kepala Gudang',
                        'role' => 'kepala_gudang',
                        'password' => Hash::make('password123')
                    ]);
                    
                    $this->info('✅ Existing user updated successfully!');
                    $this->line('   Password: password123');
                } else {
                    $this->info('❌ Operation cancelled.');
                    return 0;
                }
            } else {
                // Create new user
                $newUser = User::create([
                    'name' => 'Kepala Gudang',
                    'email' => 'kepala_gudang@example.com',
                    'password' => Hash::make('password123'),
                    'role' => 'kepala_gudang'
                ]);
                
                $this->info('✅ New kepala_gudang user created successfully!');
                $this->line("   ID: {$newUser->id}");
                $this->line("   Name: {$newUser->name}");
                $this->line("   Email: {$newUser->email}");
                $this->line("   Role: {$newUser->role}");
                $this->line("   Password: password123");
            }
            
            $this->newLine();
            $this->info('📋 Test Commands:');
            $this->line('================');
            $this->line('# Login as kepala_gudang');
            $this->line('curl -X POST http://localhost:8000/api/v1/auth/login \\');
            $this->line('  -H "Content-Type: application/json" \\');
            $this->line('  -d \'{');
            $this->line('    "email": "kepala_gudang@example.com",');
            $this->line('    "password": "password123"');
            $this->line('  }\'');
            $this->newLine();
            
            $this->line('# Test tabung-datang API (replace YOUR_TOKEN_HERE)');
            $this->line('curl -X POST http://localhost:8000/api/v1/mobile/tabung-datang \\');
            $this->line('  -H "Content-Type: application/json" \\');
            $this->line('  -H "Authorization: Bearer YOUR_TOKEN_HERE" \\');
            $this->line('  -d \'{');
            $this->line('    "lokasi": "GDG-001",');
            $this->line('    "armada": "ARM-001",');
            $this->line('    "tabung_qr": ["T-001", "T-002", "T-003"],');
            $this->line('    "keterangan": "Tabung dalam kondisi baik"');
            $this->line('  }\'');
            $this->newLine();
            
            $this->info('🎉 Kepala Gudang user setup completed!');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line('File: ' . $e->getFile());
            $this->line('Line: ' . $e->getLine());
            return 1;
        }
    }
}
