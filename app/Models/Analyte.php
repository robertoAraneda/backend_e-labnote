<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Analyte extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected  $fillable = [
        'name',
        'slug',
        'clinical_information',
        'loinc_id',
        'process_time_id',
        'workarea_id',
        'availability_id',
        'process_time_id',
        'medical_request_type_id',
        'is_patient_codable',
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
     * @return BelongsToMany
     */
    public function samplingConditions(): BelongsToMany
    {
        return $this->belongsToMany(SamplingCondition::class, 'analyte_sampling_condition')
            ->withPivot('user_id', 'created_at', 'updated_at')
            ->withTimestamps();
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
    public function availability(): BelongsTo
    {
        return $this->belongsTo(Disponibility::class);
    }

    /**
     * @return BelongsTo
     */
    public function processTime(): BelongsTo
    {
        return $this->belongsTo(ProcessTime::class);
    }

    /**
     * @return BelongsTo
     */
    public function workarea(): BelongsTo
    {
        return $this->belongsTo(Workarea::class);
    }

    /**
     * @return BelongsTo
     */
    public function medicalRequestType(): BelongsTo
    {
        return $this->belongsTo(MedicalRequestType::class);
    }
}
