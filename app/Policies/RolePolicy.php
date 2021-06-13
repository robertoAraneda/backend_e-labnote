<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return in_array('role.create', $user->getAllPermissions()->pluck('name')->toArray());
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
