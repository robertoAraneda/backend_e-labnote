<?php

namespace App\Policies;

use App\Models\SamplingIndication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SamplingIndicationPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('samplingIndication.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SamplingIndication $samplingIndication
     * @return bool
     */
    public function view(User $user, SamplingIndication $samplingIndication): bool
    {
        return in_array('samplingIndication.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('samplingIndication.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SamplingIndication $samplingIndication
     * @return bool
     */
    public function update(User $user, SamplingIndication $samplingIndication)
    {
        return in_array('samplingIndication.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SamplingIndication $samplingIndication
     * @return bool
     */
    public function delete(User $user, SamplingIndication $samplingIndication): bool
    {
        return in_array('samplingIndication.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SamplingIndication  $samplingIndication
     * @return mixed
     */
    public function restore(User $user, SamplingIndication $samplingIndication)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SamplingIndication  $samplingIndication
     * @return mixed
     */
    public function forceDelete(User $user, SamplingIndication $samplingIndication)
    {
        //
    }
}
