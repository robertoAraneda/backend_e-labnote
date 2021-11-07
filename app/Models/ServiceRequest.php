<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'authored_on';

    protected $fillable = [
        'requisition',
        'occurrence',
        'note',
        'diagnosis',
        'is_confidential',
        'date_requisition_fragment',
        'correlative_number',
        'service_request_status_id',
        'service_request_intent_id',
        'service_request_priority_id',
        'service_request_category_id',
        'patient_id',
        'requester_id',
        'performer_id',
        'location_id',
        'updated_user_id',
        'deleted_user_id',
        'created_user_ip',
        'updated_user_ip',
        'deleted_user_ip'
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function getPerPage(): int
    {
        return env('DEFAULT_PER_PAGE');
    }

    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function updatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }


    public function deletedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_user_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestStatus::class, 'service_request_status_id');
    }

    public function intent(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestIntent::class, 'service_request_intent_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestPriority::class, 'service_request_priority_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestCategory::class, 'service_request_category_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(Practitioner::class, 'performer_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function observations(): HasMany
    {
        return $this->hasMany(ServiceRequestObservation::class, 'service_request_id', 'id');
    }

    public function specimens(): HasMany
    {
        return $this->hasMany(Specimen::class, 'service_request_id', 'id');
    }
}
