<?php

namespace App\Policies;

use App\Models\Tabung;
use App\Models\User;

class TabungPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_tabung');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tabung $tabung): bool
    {
        return $user->can('view_tabung');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tabung');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tabung $tabung): bool
    {
        return $user->can('edit_tabung');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tabung $tabung): bool
    {
        return $user->can('delete_tabung');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tabung $tabung): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tabung $tabung): bool
    {
        return false;
    }
}
