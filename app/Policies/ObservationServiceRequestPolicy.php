<?php

namespace App\Policies;

use App\Models\ObservationServiceRequest;
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
     * @param ObservationServiceRequest $observationServiceRequest
     * @return bool
     */
    public function view(User $user, ObservationServiceRequest $observationServiceRequest): bool
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
     * @param ObservationServiceRequest $observationServiceRequest
     * @return bool
     */
    public function update(User $user, ObservationServiceRequest $observationServiceRequest): bool
    {
        return in_array('observationServiceRequest.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param ObservationServiceRequest $observationServiceRequest
     * @return bool
     */
    public function delete(User $user, ObservationServiceRequest $observationServiceRequest): bool
    {
        return in_array('observationServiceRequest.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ObservationServiceRequest  $observationServiceRequest
     * @return mixed
     */
    public function restore(User $user, ObservationServiceRequest $observationServiceRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ObservationServiceRequest  $observationServiceRequest
     * @return mixed
     */
    public function forceDelete(User $user, ObservationServiceRequest $observationServiceRequest)
    {
        //
    }
}
