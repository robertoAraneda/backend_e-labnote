<?php

namespace App\Policies;

use App\Models\SampleType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleTypePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('sampleType.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SampleType $sampleType
     * @return bool
     */
    public function view(User $user, SampleType $sampleType): bool
    {
        return in_array('sampleType.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('sampleType.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SampleType $sampleType
     * @return bool
     */
    public function update(User $user, SampleType $sampleType): bool
    {
        return in_array('sampleType.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * @param User $user
     * @param SampleType $sampleType
     * @return bool
     */
    public function delete(User $user, SampleType $sampleType): bool
    {
        return in_array('sampleType.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SampleType  $sampleType
     * @return mixed
     */
    public function restore(User $user, SampleType $sampleType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SampleType  $sampleType
     * @return mixed
     */
    public function forceDelete(User $user, SampleType $sampleType)
    {
        //
    }
}
