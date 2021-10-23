<?php

namespace App\Policies;

use App\Models\Specimen;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecimenPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array('specimen.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function view(User $user, Specimen $specimen): bool
    {
        return in_array('specimen.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function create(User $user): bool
    {
        return in_array('specimen.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function update(User $user, Specimen $specimen): bool
    {
        return in_array('specimen.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function delete(User $user, Specimen $specimen): bool
    {
        return in_array('specimen.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    public function restore(User $user, Specimen $specimen)
    {
        //
    }

    public function forceDelete(User $user, Specimen $specimen)
    {
        //
    }
}
