<?php

namespace App\Policies;

use App\Models\ServiceRequestStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestStatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('serviceRequestStatus.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, ServiceRequestStatus $serviceRequestStatus): bool
    {
        return in_array('serviceRequestStatus.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('serviceRequestStatus.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, ServiceRequestStatus $serviceRequestStatus): bool
    {
        return in_array('serviceRequestStatus.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, ServiceRequestStatus $serviceRequestStatus): bool
    {
        return in_array('serviceRequestStatus.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestStatus  $serviceRequestStatus
     * @return mixed
     */
    public function restore(User $user, ServiceRequestStatus $serviceRequestStatus)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestStatus  $serviceRequestStatus
     * @return mixed
     */
    public function forceDelete(User $user, ServiceRequestStatus $serviceRequestStatus)
    {
        //
    }
}
