<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequestObservationCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "service_request_observation_codes";


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'clinical_information',
        'name',
        'slug',
        'loinc_num',
        'process_time_id',
        'location_id',
        'analyte_id',
        'container_id',
        'laboratory_id',
        'availability_id',
        'process_time_id',
        'medical_request_type_id',
        'specimen_code_id',
        'medical_request_type_id',
        'active',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id',
        'created_user_ip',
        'updated_user_ip',
        'deleted_user_ip'
    ];

    /**
     * @return string
     */
    public function getPerPage(): string
    {
        return env('DEFAULT_PER_PAGE');
    }

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
        return $this->belongsToMany(SamplingCondition::class, 'observation_sampling_condition')
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
        return $this->belongsTo(Availability::class);
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
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return BelongsTo
     */
    public function specimenCode(): BelongsTo
    {
        return $this->belongsTo(SpecimenCode::class);
    }

    /**
     * @return BelongsTo
     */
    public function analyte(): BelongsTo
    {
        return $this->belongsTo(Analyte::class);
    }


    /**
     * @return BelongsTo
     */
    public function loinc(): BelongsTo
    {
        return $this->belongsTo(Loinc::class, 'loinc_num', 'loinc_num');
    }

    /**
     * @return BelongsTo
     */
    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    /**
     * @return BelongsTo
     */
    public function medicalRequestType(): BelongsTo
    {
        return $this->belongsTo(MedicalRequestType::class);
    }
}
