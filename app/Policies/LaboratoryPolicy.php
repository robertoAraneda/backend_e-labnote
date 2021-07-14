<?php

namespace App\Policies;

use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaboratoryPolicy
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
        return in_array('laboratory.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Laboratory $laboratory
     * @return mixed
     */
    public function view(User $user, Laboratory $laboratory)
    {
        return in_array('laboratory.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('laboratory.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Laboratory $laboratory
     * @return bool
     */
    public function update(User $user, Laboratory $laboratory): bool
    {
        return in_array('laboratory.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Laboratory $laboratory
     * @return bool
     */
    public function delete(User $user, Laboratory $laboratory): bool
    {
        return in_array('laboratory.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Laboratory $laboratory
     * @return mixed
     */
    public function restore(User $user, Laboratory $laboratory)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Laboratory $laboratory
     * @return mixed
     */
    public function forceDelete(User $user, Laboratory $laboratory)
    {
        //
    }
}
