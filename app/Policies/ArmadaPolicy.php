<?php

namespace App\Policies;

use App\Models\Armada;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArmadaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin utama, admin umum, kepala gudang, dan operator retail bisa melihat semua armada
        return $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang', 'operator_retail']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Armada $armada): bool
    {
        // Role yang bisa melihat detail armada
        return $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang', 'operator_retail', 'driver']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin dan kepala gudang yang bisa menambah armada
        return $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Armada $armada): bool
    {
        // Hanya admin dan kepala gudang yang bisa update armada
        return $user->hasAnyRole(['admin_utama', 'admin_umum', 'kepala_gudang']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Armada $armada): bool
    {
        // Hanya admin utama dan kepala gudang yang bisa hapus armada
        return $user->hasAnyRole(['admin_utama', 'kepala_gudang']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Armada $armada): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Armada $armada): bool
    {
        return false;
    }
}
