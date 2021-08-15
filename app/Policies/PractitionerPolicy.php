<?php

namespace App\Policies;

use App\Models\Practitioner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PractitionerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('practitioner.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, Practitioner $practitioner): bool
    {
        return in_array('practitioner.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('practitioner.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, Practitioner $practitioner): bool
    {
        return in_array('practitioner.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, Practitioner $practitioner): bool
    {
        return in_array('practitioner.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }


    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Practitioner  $practitioner
     * @return mixed
     */
    public function restore(User $user, Practitioner $practitioner)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Practitioner  $practitioner
     * @return mixed
     */
    public function forceDelete(User $user, Practitioner $practitioner)
    {
        //
    }
}
