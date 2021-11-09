<?php

namespace App\Http\Resources;

use App\Models\ServiceRequestObservationCode;
use App\Models\SpecimenCode;
use App\Models\SpecimenStatus;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            'requisition' => $this->requisition,
            'occurrence' => Carbon::parse($this->occurrence)->format('d/m/Y h:i:s'),
            'created_user_ip' => $this->created_user_ip,
            'updated_user_ip' => $this->updated_user_ip,
            'is_confidential' => (boolean) $this->is_confidential,
            'diagnosis' => $this->diagnosis,
            'authored_on' => $this->date($this->authored_on),
            'updated_at' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route(
                        'api.service-requests.show',
                        ['service_request' => $this->id],
                        false),
                ],
                'observations'  => [
                    'href' => route('api.service-request.observations', ['service_request' => $this->id], false),
                    'collection' => $this->observations->map(function($observation){
                        return  $observation->code;
                    })
                ],
                'specimens'  => [
                    'href' => route('api.service-request.specimens', ['service_request' => $this->id], false),
                    'collection' => $this->specimens->map(function($specimen){
                        return [
                            'specimen' => $specimen,
                            'specimen_code' => $specimen->code,
                            'specimen_status' => $specimen->status,
                            'container' => $specimen->container,
                            'collector' => $specimen->collector];
                    })
                ],
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
                'status' => $this->status($this->status),
                'intent' => $this->intent($this->intent),
                'priority' => $this->priority($this->priority),
                'category' => $this->category($this->category),
                'patient' => $this->patient($this->patient),
                'requester' => $this->requester($this->requester),
                'performer' => $this->performer($this->performer),
                'location' => $this->location($this->location),

            ],
        ];
    }

    private function date($date): ?string
    {
        if (!isset($date)) return null;

        return $date->format('d/m/Y H:i:s');
    }


    private function user($user): ?array
    {
        if (!isset($user)) return null;

        return [
            'id' => $user->id,
            'name' => $user->names,
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $user->id], false)
                ]
            ]
        ];
    }

    private function status($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->display,
            'code' => $payload->code,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-statuses.show', ['service_request_status' => $payload->id], false)
                ]
            ]
        ];
    }

    private function intent($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-intents.show', ['service_request_intent' => $payload->id], false)
                ]
            ]
        ];
    }

    private function priority($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-priorities.show', ['service_request_priority' => $payload->id], false)
                ]
            ]
        ];
    }

    private function category($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-categories.show', ['service_request_category' => $payload->id], false)
                ]
            ]
        ];
    }

    private function requester($payload): ?array
    {

        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->names,
            'father_family' => $payload->lastname,
            'mother_family' => $payload->mother_lastname,
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $payload->id], false)
                ]
            ]
        ];
    }

    private function performer($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'given' => $payload->given,
            'family' => $payload->family,
            '_links' => [
                'self' => [
                    'href' => route('api.practitioners.show', ['practitioner' => $payload->id], false)
                ]
            ]
        ];

    }

    private function location($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->name,
            '_links' => [
                'self' => [
                    'href' => route('api.locations.show', ['location' => $payload->id], false)
                ]
            ]
        ];
    }

    private function patient($payload): ?array
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
                        'mother_family' => $name->mother_family,
                        'text' => $name->given." ".$name->father_family." ".$name->mother_family
                    ];
                }),
            'birthdate' => Carbon::parse($payload->birthdate)->format('d/m/Y'),
            'administrative_gender' => $payload->administrativeGender->display,
            'telecom' => $payload->contactPointPatient->map(function($telecom){
                return [
                    'system' => $telecom->system,
                    'use' => $telecom->use,
                    'value' => $telecom->value
                ];
            }),
            'identifier' => $payload->identifierPatient
                ->filter(function($identifier){
                    return $identifier->identifierUse->code == 'usual' || $identifier->identifierUse->code == 'official' ;
                })
                ->map(function($identifier){
                    return [
                        'use' => $identifier->identifierUse->display,
                        'type' => $identifier->identifierType->display,
                        'value' => $identifier->value,
                    ];
                }),
            '_links' => [
                'self' => [
                    'href' => route('api.patients.show', ['patient' => $payload->id], false)
                ]
            ]
        ];
    }


}
