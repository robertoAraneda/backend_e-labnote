<?php

namespace App\Policies;

use App\Models\Loinc;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoincPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('loinc.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Loinc $loinc
     * @return bool
     */
    public function view(User $user, Loinc $loinc): bool
    {
        return in_array('loinc.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('loinc.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Loinc $loinc
     * @return bool
     */
    public function update(User $user, Loinc $loinc): bool
    {
        return in_array('loinc.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Loinc $loinc
     * @return bool
     */
    public function delete(User $user, Loinc $loinc): bool
    {
        return in_array('loinc.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Loinc $loinc
     * @return mixed
     */
    public function restore(User $user, Loinc $loinc)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Loinc $loinc
     * @return mixed
     */
    public function forceDelete(User $user, Loinc $loinc)
    {
        //
    }
}
