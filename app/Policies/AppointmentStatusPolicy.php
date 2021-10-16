<?php

namespace App\Policies;

use App\Models\AppointmentStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentStatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return in_array('appointmentStatus.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, AppointmentStatus $appointmentStatus)
    {
        return in_array('appointmentStatus.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user)
    {
        return in_array('appointmentStatus.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, AppointmentStatus $appointmentStatus)
    {
        return in_array('appointmentStatus.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, AppointmentStatus $appointmentStatus)
    {
        return in_array('appointmentStatus.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppointmentStatus  $appointmentStatus
     * @return mixed
     */
    public function restore(User $user, AppointmentStatus $appointmentStatus)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppointmentStatus  $appointmentStatus
     * @return mixed
     */
    public function forceDelete(User $user, AppointmentStatus $appointmentStatus)
    {
        //
    }
}
