<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'start' => $this->start,
            'end' => $this->end,
            'description' => $this->description,
            'appointment_status_id' => $this->appointment_status_id,
            'patient_id' => $this->patient_id,
            'service_request_id'=> $this->service_request_id,
            'minutes_duration' => (int) $this->minutes_duration,
            'created_user_ip' => $this->created_user_ip,
            'updated_user_ip' => $this->updated_user_ip,
            'created_at' => $this->date($this->created_at),
            'updated_at' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route('api.appointments.show', ['appointment' => $this->id], false),
                ],
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
            ],
        ];
    }

    private function date($date): ?string
    {
        if(!isset($date)) return null;

        return $date->format('d/m/Y h:i:s');
    }


    private function user($user): ?array
    {
        if(!isset($user)) return null;

        return [
            'name' => $user->names,
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $user->id], false)
                ]
            ]
        ];
    }
}
