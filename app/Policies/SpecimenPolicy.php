<?php

namespace App\Policies;

use App\Models\Specimen;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecimenPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        if (in_array('specimen.index', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }
        if (in_array('observationServiceRequest.create', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }
    }

    /**
     * @param User $user
     * @param Specimen $sampleType
     * @return bool
     */
    public function view(User $user, Specimen $specimen): bool
    {
        return in_array('specimen.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('specimen.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param Specimen $sampleType
     * @return bool
     */
    public function update(User $user, Specimen $specimen): bool
    {
        return in_array('specimen.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param Specimen $sampleType
     * @return bool
     */
    public function delete(User $user, Specimen $specimen): bool
    {
        return in_array('specimen.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specimen  $sampleType
     * @return mixed
     */
    public function restore(User $user, Specimen $specimen)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specimen  $sampleType
     * @return mixed
     */
    public function forceDelete(User $user, Specimen $specimen)
    {
        //
    }
}
