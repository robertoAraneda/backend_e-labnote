<?php

namespace App\Policies;

use App\Models\Analyte;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnalytePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return in_array('analyte.index', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Analyte $analyte
     * @return bool
     */
    public function view(User $user, Analyte $analyte): bool
    {
        return in_array('analyte.show', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('analyte.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Analyte $analyte
     * @return bool
     */
    public function update(User $user, Analyte $analyte): bool
    {
        return in_array('analyte.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Analyte $analyte
     * @return bool
     */
    public function delete(User $user, Analyte $analyte): bool
    {
        return in_array('analyte.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Analyte $analyte
     * @return mixed
     */
    public function restore(User $user, Analyte $analyte)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Analyte $analyte
     * @return mixed
     */
    public function forceDelete(User $user, Analyte $analyte)
    {
        //
    }
}
