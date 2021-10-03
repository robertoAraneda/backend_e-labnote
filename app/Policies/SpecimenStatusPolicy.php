<?php

namespace App\Policies;

use App\Models\SpecimenStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecimenStatusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if (in_array('specimenStatus.index', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }
        if (in_array('observationServiceRequest.create', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecimenStatus  $specimenStatus
     * @return mixed
     */
    public function view(User $user, SpecimenStatus $specimenStatus)
    {
        return in_array('specimenStatus.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array('specimenStatus.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecimenStatus  $specimenStatus
     * @return mixed
     */
    public function update(User $user, SpecimenStatus $specimenStatus)
    {
        return in_array('specimenStatus.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecimenStatus  $specimenStatus
     * @return mixed
     */
    public function delete(User $user, SpecimenStatus $specimenStatus)
    {
        return in_array('specimenStatus.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecimenStatus  $specimenStatus
     * @return mixed
     */
    public function restore(User $user, SpecimenStatus $specimenStatus)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecimenStatus  $specimenStatus
     * @return mixed
     */
    public function forceDelete(User $user, SpecimenStatus $specimenStatus)
    {
        //
    }
}
