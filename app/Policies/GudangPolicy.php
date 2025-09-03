<?php

namespace App\Policies;

use App\Models\Gudang;
use App\Models\User;

class GudangPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_gudang');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Gudang $gudang): bool
    {
        return $user->can('view_gudang');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_gudang');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Gudang $gudang): bool
    {
        return $user->can('edit_gudang');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Gudang $gudang): bool
    {
        return $user->can('delete_gudang');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Gudang $gudang): bool
    {
        return $user->can('restore_gudang');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Gudang $gudang): bool
    {
        return $user->can('force_delete_gudang');
    }
}
