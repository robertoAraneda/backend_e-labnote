<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('location.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, Location $location): bool
    {
        return in_array('location.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('location.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, Location $location): bool
    {
        return in_array('location.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, Location $location): bool
    {
        return in_array('location.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }


    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Location  $location
     * @return mixed
     */
    public function restore(User $user, Location $location)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Location  $location
     * @return mixed
     */
    public function forceDelete(User $user, Location $location)
    {
        //
    }
}
