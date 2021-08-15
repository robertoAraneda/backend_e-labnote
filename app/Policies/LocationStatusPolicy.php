<?php

namespace App\Policies;

use App\Models\LocationStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationStatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('locationStatus.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, LocationStatus $locationStatus): bool
    {
        return in_array('locationStatus.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('locationStatus.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, LocationStatus $locationStatus): bool
    {
        return in_array('locationStatus.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, LocationStatus $locationStatus): bool
    {
        return in_array('locationStatus.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LocationStatus  $locationStatus
     * @return mixed
     */
    public function restore(User $user, LocationStatus $locationStatus)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LocationStatus  $locationStatus
     * @return mixed
     */
    public function forceDelete(User $user, LocationStatus $locationStatus)
    {
        //
    }
}
