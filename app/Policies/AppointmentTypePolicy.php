<?php

namespace App\Policies;

use App\Models\AppointmentType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return in_array('appointmentType.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, AppointmentType $appointmentType)
    {
        return in_array('appointmentType.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user)
    {
        return in_array('appointmentType.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, AppointmentType $appointmentType)
    {
        return in_array('appointmentType.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, AppointmentType $appointmentType)
    {
        return in_array('appointmentType.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppointmentType  $appointmentType
     * @return mixed
     */
    public function restore(User $user, AppointmentType $appointmentType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppointmentType  $appointmentType
     * @return mixed
     */
    public function forceDelete(User $user, AppointmentType $appointmentType)
    {
        //
    }
}
