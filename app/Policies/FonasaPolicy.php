<?php

namespace App\Policies;

use App\Models\Fonasa;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FonasaPolicy
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
     * @param Fonasa $fonasa
     * @return mixed
     */
    public function view(User $user, Fonasa $fonasa)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('fonasa.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Fonasa $fonasa
     * @return bool
     */
    public function update(User $user, Fonasa $fonasa): bool
    {
        return in_array('fonasa.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Fonasa $fonasa
     * @return bool
     */
    public function delete(User $user, Fonasa $fonasa): bool
    {
        return in_array('fonasa.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Fonasa $fonasa
     * @return mixed
     */
    public function restore(User $user, Fonasa $fonasa)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Fonasa $fonasa
     * @return mixed
     */
    public function forceDelete(User $user, Fonasa $fonasa)
    {
        //
    }
}
