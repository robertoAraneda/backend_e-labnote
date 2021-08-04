<?php

namespace App\Policies;

use App\Models\AdministrativeGender;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdministrativeGenderPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        if (in_array('administrativeGender.index', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }
        if (in_array('patient.create', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }
       return false;
    }

    /**
     * @param User $user
     * @param AdministrativeGender $gender
     * @return bool
     */
    public function view(User $user, AdministrativeGender $gender): bool
    {
        return in_array('administrativeGender.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('administrativeGender.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param AdministrativeGender $gender
     * @return bool
     */
    public function update(User $user, AdministrativeGender $gender): bool
    {
        return in_array('administrativeGender.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param AdministrativeGender $gender
     * @return bool
     */
    public function delete(User $user, AdministrativeGender $gender): bool
    {
        return in_array('administrativeGender.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AdministrativeGender  $gender
     * @return mixed
     */
    public function restore(User $user, AdministrativeGender $gender)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AdministrativeGender  $gender
     * @return mixed
     */
    public function forceDelete(User $user, AdministrativeGender $gender)
    {
        //
    }
}
