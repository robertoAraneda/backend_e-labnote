<?php

namespace App\Policies;

use App\Models\ServiceRequestIntent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestIntentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('serviceRequestIntent.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, ServiceRequestIntent $serviceRequestIntent): bool
    {
        return in_array('serviceRequestIntent.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('serviceRequestIntent.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, ServiceRequestIntent $serviceRequestIntent): bool
    {
        return in_array('serviceRequestIntent.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, ServiceRequestIntent $serviceRequestIntent): bool
    {
        return in_array('serviceRequestIntent.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestIntent  $serviceRequestIntent
     * @return mixed
     */
    public function restore(User $user, ServiceRequestIntent $serviceRequestIntent)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestIntent  $serviceRequestIntent
     * @return mixed
     */
    public function forceDelete(User $user, ServiceRequestIntent $serviceRequestIntent)
    {
        //
    }
}
