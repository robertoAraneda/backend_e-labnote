<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Role $role): bool
    {
        return in_array('rolePermission.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        $roleCreate = in_array('role.create', $user->getAllPermissions()->pluck('name')->toArray());

        if($roleCreate){
            return true;
        }

        $rolePermissionCreate = in_array('rolePermission.create', $user->getAllPermissions()->pluck('name')->toArray());
        if($rolePermissionCreate){
            return true;
        }

        return false;
    }

    public function update(User $user, Role $role): bool
    {
        return in_array('role.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, Role $role): bool
    {
        return in_array('role.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }
}
