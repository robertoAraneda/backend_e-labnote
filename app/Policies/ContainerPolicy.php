<?php

namespace App\Policies;

use App\Models\Container;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContainerPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        if (in_array('container.index', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }
        if (in_array('observationServiceRequest.create', $user->getAllPermissions()->pluck('name')->toArray())) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Container $container
     * @return bool
     */
    public function view(User $user, Container $container): bool
    {
        return in_array('container.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('container.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Container $container
     * @return bool
     */
    public function update(User $user, Container $container): bool
    {
        return in_array('container.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param Container $container
     * @return bool
     */
    public function delete(User $user, Container $container): bool
    {
        return in_array('container.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Container $container
     * @return mixed
     */
    public function restore(User $user, Container $container)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Container $container
     * @return mixed
     */
    public function forceDelete(User $user, Container $container)
    {
        //
    }
}
