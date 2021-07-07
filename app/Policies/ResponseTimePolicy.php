<?php

namespace App\Policies;

use App\Models\ResponseTime;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResponseTimePolicy
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
     * @param ResponseTime $responseTime
     * @return mixed
     */
    public function view(User $user, ResponseTime $responseTime)
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
        return in_array('responseTime.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param ResponseTime $responseTime
     * @return bool
     */
    public function update(User $user, ResponseTime $responseTime): bool
    {
        return in_array('responseTime.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param ResponseTime $responseTime
     * @return bool
     */
    public function delete(User $user, ResponseTime $responseTime): bool
    {
        return in_array('responseTime.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param ResponseTime $responseTime
     * @return mixed
     */
    public function restore(User $user, ResponseTime $responseTime)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param ResponseTime $responseTime
     * @return mixed
     */
    public function forceDelete(User $user, ResponseTime $responseTime)
    {
        //
    }
}
