<?php

namespace App\Policies;

use App\Models\MedicalRequestType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalRequestTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param MedicalRequestType $medicalRequestType
     * @return mixed
     */
    public function view(User $user, MedicalRequestType $medicalRequestType)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return in_array('medicalRequestType.create', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param MedicalRequestType $medicalRequestType
     * @return bool
     */
    public function update(User $user, MedicalRequestType $medicalRequestType): bool
    {
        return in_array('medicalRequestType.update', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param MedicalRequestType $medicalRequestType
     * @return bool
     */
    public function delete(User $user, MedicalRequestType $medicalRequestType): bool
    {
        return in_array('medicalRequestType.delete', $user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param MedicalRequestType $medicalRequestType
     * @return mixed
     */
    public function restore(User $user, MedicalRequestType $medicalRequestType)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param MedicalRequestType $medicalRequestType
     * @return mixed
     */
    public function forceDelete(User $user, MedicalRequestType $medicalRequestType)
    {
        //
    }
}
