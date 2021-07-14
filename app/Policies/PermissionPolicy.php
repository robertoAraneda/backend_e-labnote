<?php

namespace App\Policies;


use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user)
    {
        return in_array('permission.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, Permission $permission)
    {
        return in_array('permission.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('permission.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, Permission $permission): bool
    {
        return in_array('permission.update', $user->getAllPermissions()->pluck('name')->toArray());
    }


    public function delete(User $user, Permission $permission): bool
    {
        return in_array('permission.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function restore(User $user, Permission $permission)
    {
        //
    }

    public function forceDelete(User $user, Permission $permission)
    {
        //
    }
}
