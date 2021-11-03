<?php

namespace App\Http\Resources\Collections;

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
            'service_request_id' => (int) $this->service_request_id,
            'minutes_duration' => (int) $this->minutes_duration,
            'description' => $this->description,
            'slot' => $this->slot,
            'status' => $this->status,
            'type' => $this->type,
            'patient' => $this->getPatient($this->patient),
            '_links' => [
                'self' =>[
                    'href' => route('api.appointments.show', ['appointment' => $this->id], false),
                ] ,
            ],
        ];
    }

    private function getPatient($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->humanNames
                ->filter(function ($name) {
                    return $name->use == 'usual' || $name->use == 'official';
                })
                ->map(function ($name) {
                    return [
                        'use' => $name->use,
                        'given' => $name->given,
                        'father_family' => $name->father_family,
                        'mother_family' => $name->mother_family];
                }),
            '_links' => [
                'self' => [
                    'href' => route('api.patients.show', ['patient' => $payload->id], false)
                ]
            ]
        ];
    }
}
