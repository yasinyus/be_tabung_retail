<?php

namespace App\Policies;

use App\Models\Armada;
use App\Models\User;

class ArmadaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_armada');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Armada $armada): bool
    {
        return $user->can('view_armada');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_armada');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Armada $armada): bool
    {
        return $user->can('edit_armada');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Armada $armada): bool
    {
        return $user->can('delete_armada');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Armada $armada): bool
    {
        return $user->can('restore_armada');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Armada $armada): bool
    {
        return $user->can('force_delete_armada');
    }
}
