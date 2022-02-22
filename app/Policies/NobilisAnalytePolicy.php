<?php

namespace App\Policies;

use App\Models\NobilisAnalyte;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NobilisAnalytePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return in_array('nobilisAnalyte.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, NobilisAnalyte $nobilisAnalyte)
    {
        return in_array('nobilisAnalyte.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user)
    {
        return in_array('nobilisAnalyte.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, NobilisAnalyte $nobilisAnalyte)
    {
        return in_array('nobilisAnalyte.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, NobilisAnalyte $nobilisAnalyte)
    {
        return in_array('nobilisAnalyte.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function restore(User $user, NobilisAnalyte $nobilisAnalyte)
    {
        //
    }

    public function forceDelete(User $user, NobilisAnalyte $nobilisAnalyte)
    {
        //
    }
}
