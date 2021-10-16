<?php

namespace App\Http\Resources\collections;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'start' =>$this->start,
            'end' =>  $this->end,
            'appointment_status_id' => (int) $this->appointment_status_id,
            'patient_id' => (int) $this->patient_id,
            'service_request_id' => (int) $this->service_request_id,
            'minutes_duration' => (int) $this->minutes_duration,
            'description' => $this->description,
            'slot' => $this->slot,
            'status' => $this->status,
            'type' => $this->type,
            'patient' => $this->patient->humanNames,
            '_links' => [
                'self' =>[
                    'href' => route('api.appointments.show', ['appointment' => $this->id], false),
                ] ,
            ],
        ];
    }
}
