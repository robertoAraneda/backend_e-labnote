<?php

namespace App\Policies;

use App\Models\ServiceRequestPriority;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestPriorityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('serviceRequestPriority.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, ServiceRequestPriority $serviceRequestPriority): bool
    {
        return in_array('serviceRequestPriority.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('serviceRequestPriority.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, ServiceRequestPriority $serviceRequestPriority): bool
    {
        return in_array('serviceRequestPriority.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, ServiceRequestPriority $serviceRequestPriority): bool
    {
        return in_array('serviceRequestPriority.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestPriority  $serviceRequestPriority
     * @return mixed
     */
    public function restore(User $user, ServiceRequestPriority $serviceRequestPriority)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestPriority  $serviceRequestPriority
     * @return mixed
     */
    public function forceDelete(User $user, ServiceRequestPriority $serviceRequestPriority)
    {
        //
    }
}
