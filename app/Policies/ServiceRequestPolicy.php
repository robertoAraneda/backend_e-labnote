<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('serviceRequest.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        return in_array('serviceRequest.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('serviceRequest.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        return in_array('serviceRequest.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return in_array('serviceRequest.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequest  $serviceRequest
     * @return mixed
     */
    public function restore(User $user, ServiceRequest $serviceRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequest  $serviceRequest
     * @return mixed
     */
    public function forceDelete(User $user, ServiceRequest $serviceRequest)
    {
        //
    }
}
