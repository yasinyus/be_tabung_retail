<?php

namespace App\Policies;

use App\Models\Gudang;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GudangPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin_utama', 'admin', 'operator', 'viewer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Gudang $gudang): bool
    {
        return $user->hasAnyRole(['admin_utama', 'admin', 'operator', 'viewer']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin_utama', 'admin', 'operator']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Gudang $gudang): bool
    {
        return $user->hasAnyRole(['admin_utama', 'admin', 'operator']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Gudang $gudang): bool
    {
        return $user->hasAnyRole(['admin_utama', 'admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Gudang $gudang): bool
    {
        return $user->hasRole('admin_utama');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Gudang $gudang): bool
    {
        return $user->hasRole('admin_utama');
    }
}
