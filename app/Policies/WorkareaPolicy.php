<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workarea;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkareaPolicy
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
     * @param Workarea $workarea
     * @return mixed
     */
    public function view(User $user, Workarea $workarea)
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
        return in_array('workarea.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Workarea $workarea
     * @return bool
     */
    public function update(User $user, Workarea $workarea): bool
    {
        return in_array('workarea.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Workarea $workarea
     * @return mixed
     */
    public function delete(User $user, Workarea $workarea)
    {
        return in_array('workarea.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Workarea $workarea
     * @return mixed
     */
    public function restore(User $user, Workarea $workarea)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Workarea $workarea
     * @return mixed
     */
    public function forceDelete(User $user, Workarea $workarea)
    {
        //
    }
}
