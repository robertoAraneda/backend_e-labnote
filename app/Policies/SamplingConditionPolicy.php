<?php

namespace App\Policies;

use App\Models\SamplingCondition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SamplingConditionPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('samplingCondition.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SamplingCondition $samplingCondition
     * @return bool
     */
    public function view(User $user, SamplingCondition $samplingCondition): bool
    {
        return in_array('samplingCondition.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('samplingCondition.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param SamplingCondition $samplingCondition
     * @return bool
     */
    public function update(User $user, SamplingCondition $samplingCondition): bool
    {
        return in_array('samplingCondition.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param SamplingCondition $samplingCondition
     * @return bool
     */
    public function delete(User $user, SamplingCondition $samplingCondition): bool
    {
        return in_array('samplingCondition.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param SamplingCondition $samplingCondition
     * @return mixed
     */
    public function restore(User $user, SamplingCondition $samplingCondition)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param SamplingCondition $samplingCondition
     * @return mixed
     */
    public function forceDelete(User $user, SamplingCondition $samplingCondition)
    {
        //
    }
}
