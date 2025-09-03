<?php

namespace App\Policies;

use App\Models\Tagihan;
use App\Models\User;

class TagihanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tagihan $tagihan): bool
    {
        return true; // Allow all authenticated users
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false; // Tagihan is read-only
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tagihan $tagihan): bool
    {
        return false; // Tagihan is read-only
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tagihan $tagihan): bool
    {
        return false; // Tagihan is read-only
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tagihan $tagihan): bool
    {
        return false; // Tagihan is read-only
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tagihan $tagihan): bool
    {
        return false; // Tagihan is read-only
    }
}
