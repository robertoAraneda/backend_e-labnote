<?php

namespace App\Policies;

use App\Models\Module;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModulePolicy
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
        return in_array('module.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Module $module
     * @return mixed
     */
    public function view(User $user, Module $module)
    {
        return in_array('module.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return in_array('module.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Module $module
     * @return bool
     */
    public function update(User $user, Module $module): bool
    {
        return in_array('module.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Module $module
     * @return bool
     */
    public function delete(User $user, Module $module): bool
    {
        return in_array('module.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Module $module
     * @return mixed
     */
    public function restore(User $user, Module $module)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Module $module
     * @return mixed
     */
    public function forceDelete(User $user, Module $module)
    {
        //
    }
}
