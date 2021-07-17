<?php

namespace App\Policies;

use App\Models\SampleQuantity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleQuantityPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('sampleQuantity.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SampleQuantity $sampleQuantity
     * @return bool
     */
    public function view(User $user, SampleQuantity $sampleQuantity): bool
    {
        return in_array('sampleQuantity.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('sampleQuantity.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param SampleQuantity $sampleQuantity
     * @return bool
     */
    public function update(User $user, SampleQuantity $sampleQuantity): bool
    {
        return in_array('sampleQuantity.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param SampleQuantity $sampleQuantity
     * @return bool
     */
    public function delete(User $user, SampleQuantity $sampleQuantity): bool
    {
        return in_array('sampleQuantity.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param SampleQuantity $sampleQuantity
     * @return mixed
     */
    public function restore(User $user, SampleQuantity $sampleQuantity)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param SampleQuantity $sampleQuantity
     * @return mixed
     */
    public function forceDelete(User $user, SampleQuantity $sampleQuantity)
    {
        //
    }
}
