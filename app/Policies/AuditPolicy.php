<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\User;

class AuditPolicy
{
    /**
     * Determine whether the user can view any models.
     * Allow all authenticated users to access audits
     */
    public function viewAny(User $user): bool
    {
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Audit $audit): bool
    {
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Audit $audit): bool
    {
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Audit $audit): bool
    {
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Audit $audit): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Audit $audit): bool
    {
        return $user->hasRole('admin');
    }
}
