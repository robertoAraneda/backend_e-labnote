<?php

namespace App\Policies;

use App\Models\ProcessTime;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProcessTimePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('processTime.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param ProcessTime $processTime
     * @return bool
     */
    public function view(User $user, ProcessTime $processTime): bool
    {
        return in_array('processTime.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('processTime.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ProcessTime $processTime
     * @return bool
     */
    public function update(User $user, ProcessTime $processTime): bool
    {
        return in_array('processTime.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ProcessTime $processTime
     * @return bool
     */
    public function delete(User $user, ProcessTime $processTime): bool
    {
        return in_array('processTime.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param ProcessTime $processTime
     * @return mixed
     */
    public function restore(User $user, ProcessTime $processTime)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param ProcessTime $processTime
     * @return mixed
     */
    public function forceDelete(User $user, ProcessTime $processTime)
    {
        //
    }
}
