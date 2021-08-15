<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('organization.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, Organization $organization): bool
    {
        return in_array('organization.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('organization.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, Organization $organization): bool
    {
        return in_array('organization.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, Organization $organization): bool
    {
        return in_array('organization.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return mixed
     */
    public function restore(User $user, Organization $organization)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organization  $organization
     * @return mixed
     */
    public function forceDelete(User $user, Organization $organization)
    {
        //
    }
}
