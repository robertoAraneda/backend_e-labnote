<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CityPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('city.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function view(User $user, City $city): bool
    {
        return in_array('city.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('city.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function update(User $user, City $city): bool
    {
        return in_array('city.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param City $city
     * @return bool
     */
    public function delete(User $user, City $city): bool
    {
        return in_array('city.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param City $city
     * @return mixed
     */
    public function restore(User $user, City $city)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param City $city
     * @return mixed
     */
    public function forceDelete(User $user, City $city)
    {
        //
    }
}
