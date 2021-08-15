<?php

namespace App\Policies;

use App\Models\LocationType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('locationType.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, LocationType $locationType): bool
    {
        return in_array('locationType.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('locationType.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, LocationType $locationType): bool
    {
        return in_array('locationType.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, LocationType $locationType): bool
    {
        return in_array('locationType.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LocationType  $locationType
     * @return mixed
     */
    public function restore(User $user, LocationType $locationType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LocationType  $locationType
     * @return mixed
     */
    public function forceDelete(User $user, LocationType $locationType)
    {
        //
    }
}
