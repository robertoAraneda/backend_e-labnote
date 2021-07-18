<?php

namespace App\Policies;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvailabilityPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        if (in_array('availability.index', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }
        if (in_array('observationServiceRequest.create', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Availability $availability
     * @return bool
     */
    public function view(User $user, Availability $availability): bool
    {
        return in_array('availability.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('availability.create', $user->getAllPermissions()->pluck('name')->toArray());
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Availability $availability
     * @return bool
     */
    public function update(User $user, Availability $availability): bool
    {
        return in_array('availability.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Availability $availability
     * @return mixed
     */
    public function delete(User $user, Availability $availability)
    {
        return in_array('availability.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Availability $availability
     * @return mixed
     */
    public function restore(User $user, Availability $availability)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Availability $availability
     * @return mixed
     */
    public function forceDelete(User $user, Availability $availability)
    {
        //
    }
}
