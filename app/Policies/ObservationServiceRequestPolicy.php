<?php

namespace App\Policies;

use App\Models\ServiceRequestObservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObservationServiceRequestPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('observationServiceRequest.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param ServiceRequestObservation $observationServiceRequest
     * @return bool
     */
    public function view(User $user, ServiceRequestObservation $observationServiceRequest): bool
    {
        return in_array('observationServiceRequest.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('observationServiceRequest.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param ServiceRequestObservation $observationServiceRequest
     * @return bool
     */
    public function update(User $user, ServiceRequestObservation $observationServiceRequest): bool
    {
        return in_array('observationServiceRequest.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param ServiceRequestObservation $observationServiceRequest
     * @return bool
     */
    public function delete(User $user, ServiceRequestObservation $observationServiceRequest): bool
    {
        return in_array('observationServiceRequest.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestObservation  $observationServiceRequest
     * @return mixed
     */
    public function restore(User $user, ServiceRequestObservation $observationServiceRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestObservation  $observationServiceRequest
     * @return mixed
     */
    public function forceDelete(User $user, ServiceRequestObservation $observationServiceRequest)
    {
        //
    }
}
