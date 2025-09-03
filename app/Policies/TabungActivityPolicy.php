<?php

namespace App\Policies;

use App\Models\TabungActivity;
use App\Models\User;

class TabungActivityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_tabung_activity');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TabungActivity $tabungActivity): bool
    {
        return $user->can('view_tabung_activity');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tabung_activity');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TabungActivity $tabungActivity): bool
    {
        return $user->can('edit_tabung_activity');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TabungActivity $tabungActivity): bool
    {
        return $user->can('delete_tabung_activity');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TabungActivity $tabungActivity): bool
    {
        return $user->can('restore_tabung_activity');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TabungActivity $tabungActivity): bool
    {
        return $user->can('force_delete_tabung_activity');
    }
}
