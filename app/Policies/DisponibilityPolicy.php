<?php

namespace App\Policies;

use App\Models\Disponibility;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DisponibilityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Disponibility $disponibility
     * @return mixed
     */
    public function view(User $user, Disponibility $disponibility)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('disponibility.create', $user->getAllPermissions()->pluck('name')->toArray());
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Disponibility $disponibility
     * @return bool
     */
    public function update(User $user, Disponibility $disponibility): bool
    {
        return in_array('disponibility.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Disponibility $disponibility
     * @return mixed
     */
    public function delete(User $user, Disponibility $disponibility)
    {
        return in_array('disponibility.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Disponibility $disponibility
     * @return mixed
     */
    public function restore(User $user, Disponibility $disponibility)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Disponibility $disponibility
     * @return mixed
     */
    public function forceDelete(User $user, Disponibility $disponibility)
    {
        //
    }
}
