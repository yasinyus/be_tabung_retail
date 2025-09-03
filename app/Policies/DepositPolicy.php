<?php

namespace App\Policies;

use App\Models\Deposit;
use App\Models\User;

class DepositPolicy
{
    /**
     * Determine whether the user can view any models.
     * Only admin role can access deposits
     */
    public function viewAny(User $user): bool
    {
        // return $user->hasRole('admin');
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Deposit $deposit): bool
    {
        // return $user->hasRole('admin');
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return $user->hasRole('admin');
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Deposit $deposit): bool
    {
        // return $user->hasRole('admin');
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Deposit $deposit): bool
    {
        // return $user->hasRole('admin');
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Deposit $deposit): bool
    {
        // return $user->hasRole('admin');
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Deposit $deposit): bool
    {
        // return $user->hasRole('admin');
        return true; // Allow all authenticated users
    }
}
