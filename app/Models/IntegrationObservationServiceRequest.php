<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationObservationServiceRequest extends Model
{
    use HasFactory;

    protected $table = 'integration_observation_service_requests';

    protected $fillable = [
        'lis_name',
        'observation_service_request_id',
        'model',
        'model_id',
        'active'
    ];

    public function getPerPage(): string
    {
        return env('DEFAULT_PER_PAGE');
    }

    public function serviceRequestObservationCode(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestObservationCode::class, 'observation_service_request_id');
    }

    public function nobilis(): BelongsTo
    {
        return $this->belongsTo(NobilisAnalyte::class, 'model_id');
    }
}
