<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access the admin panel.
     * SECURE: Only specific roles can access admin
     */
    public function canAccessPanel($panel): bool
    {
        // SECURITY: Only allow specific roles to access admin
        $allowedRoles = [
            'admin_utama',
            'admin_umum', 
            'kepala_gudang',
            'operator_retail',
            'driver'  // Added for debugging - dapat disesuaikan nanti
        ];
        
        return in_array($this->role, $allowedRoles);
        
        // Alternative: Temporary allow all for debugging
        // return true;
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin_utama', 'admin_umum']);
    }
    
    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin_utama';
    }
}
