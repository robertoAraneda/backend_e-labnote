<?php

namespace App\Policies;

use App\Models\ServiceRequestCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceRequestCategoryPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user): bool
    {
        return in_array('serviceRequestCategory.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, ServiceRequestCategory $serviceRequestCategory): bool
    {
        return in_array('serviceRequestCategory.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('serviceRequestCategory.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, ServiceRequestCategory $serviceRequestCategory): bool
    {
        return in_array('serviceRequestCategory.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, ServiceRequestCategory $serviceRequestCategory): bool
    {
        return in_array('serviceRequestCategory.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ServiceRequestCategory $serviceRequestCategory
     * @return mixed
     */
    public function restore(User $user, ServiceRequestCategory $serviceRequestCategory)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ServiceRequestCategory $serviceRequestCategory
     * @return mixed
     */
    public function forceDelete(User $user, ServiceRequestCategory $serviceRequestCategory)
    {
        //
    }
}
