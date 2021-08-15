<?php

namespace App\Policies;

use App\Models\SpecimenCode;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecimenCodePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        if (in_array('specimenCode.index', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }
        if (in_array('observationServiceRequest.create', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param SpecimenCode $sampleType
     * @return bool
     */
    public function view(User $user, SpecimenCode $specimen): bool
    {
        return in_array('specimenCode.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('specimenCode.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SpecimenCode $sampleType
     * @return bool
     */
    public function update(User $user, SpecimenCode $specimen): bool
    {
        return in_array('specimenCode.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SpecimenCode $sampleType
     * @return bool
     */
    public function delete(User $user, SpecimenCode $specimen): bool
    {
        return in_array('specimenCode.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecimenCode  $sampleType
     * @return mixed
     */
    public function restore(User $user, SpecimenCode $specimen)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecimenCode  $sampleType
     * @return mixed
     */
    public function forceDelete(User $user, SpecimenCode $specimen)
    {
        //
    }
}
