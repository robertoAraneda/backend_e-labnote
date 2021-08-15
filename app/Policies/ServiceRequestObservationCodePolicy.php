<?php

namespace App\Policies;

use App\Models\ServiceRequestObservationCode;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestObservationCodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return in_array('serviceRequestObservationCode.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestObservationCode  $serviceRequestObservationCode
     * @return mixed
     */
    public function view(User $user, ServiceRequestObservationCode $serviceRequestObservationCode)
    {
        return in_array('serviceRequestObservationCode.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array('serviceRequestObservationCode.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestObservationCode  $serviceRequestObservationCode
     * @return mixed
     */
    public function update(User $user, ServiceRequestObservationCode $serviceRequestObservationCode)
    {
        return in_array('serviceRequestObservationCode.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestObservationCode  $serviceRequestObservationCode
     * @return mixed
     */
    public function delete(User $user, ServiceRequestObservationCode $serviceRequestObservationCode)
    {
        return in_array('serviceRequestObservationCode.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestObservationCode  $serviceRequestObservationCode
     * @return mixed
     */
    public function restore(User $user, ServiceRequestObservationCode $serviceRequestObservationCode)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ServiceRequestObservationCode  $serviceRequestObservationCode
     * @return mixed
     */
    public function forceDelete(User $user, ServiceRequestObservationCode $serviceRequestObservationCode)
    {
        //
    }
}
