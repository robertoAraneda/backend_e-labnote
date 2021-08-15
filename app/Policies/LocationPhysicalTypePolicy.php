<?php

namespace App\Policies;

use App\Models\LocationPhysicalType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPhysicalTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('locationPhysicalType.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, LocationPhysicalType $locationPhysicalType): bool
    {
        return in_array('locationPhysicalType.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('locationPhysicalType.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, LocationPhysicalType $locationPhysicalType): bool
    {
        return in_array('locationPhysicalType.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, LocationPhysicalType $locationPhysicalType): bool
    {
        return in_array('locationPhysicalType.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LocationPhysicalType  $locationPhysicalType
     * @return mixed
     */
    public function restore(User $user, LocationPhysicalType $locationPhysicalType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LocationPhysicalType  $locationPhysicalType
     * @return mixed
     */
    public function forceDelete(User $user, LocationPhysicalType $locationPhysicalType)
    {
        //
    }
}
