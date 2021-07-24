<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected  $fillable = [
        'birthdate',
        'administrative_gender_id',
        'active',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id',
        'created_user_ip',
        'updated_user_ip',
        'deleted_user_ip'
    ];

    /**
     * Scope a query to only include active users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * @return string
     */
    public function getPerPage(): string
    {
        return env('DEFAULT_PER_PAGE');
    }

    /**
     * @return BelongsTo
     */
    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function updatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function deletedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function administrativeGender(): BelongsTo
    {
        return $this->belongsTo(AdministrativeGender::class);
    }

    /**
     * @return HasMany
     */
    public function humanNames(): HasMany
    {
        return $this->hasMany(HumanName::class, 'patient_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function contactPointPatient(): HasMany
    {
        return $this->hasMany(ContactPointPatient::class, 'patient_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function addressPatient(): HasMany
    {
        return $this->hasMany(AddressPatient::class, 'patient_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function contactPatient(): HasMany
    {
        return $this->hasMany(ContactPatient::class, 'patient_id', 'id');
    }
}
