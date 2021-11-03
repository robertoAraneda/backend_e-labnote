<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier($this->identifierPatient),
            'name' =>$this->name($this->humanNames),
            'telecom' => $this->telecom($this->contactPointPatient),
            'address' => $this->address($this->addressPatient),
            'contact' => $this->contact($this->contactPatient),
            'administrative_gender_id' => $this->administrativeGender->id,
            'birthdate' => $this->birthdate,
            'active' => (bool) $this->active,
            'created_user_ip' => $this->created_user_ip,
            'updated_user_ip' => $this->updated_user_ip,
            'created_at' => $this->date($this->created_at),
            'updated_at' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route('api.patients.show', ['patient' => $this->id], false),
                ],
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
                'administrativeGender' => $this->administrativeGender($this->administrativeGender)
            ],
        ];
    }

    /**
     * @param $date
     * @return string|null
     */

    private function date($date): ?string
    {
        if(!isset($date)) return null;

        return $date->format('d/m/Y h:i:s');
    }

    private function telecom($array){
        if(count($array) === 0) return $array;

        return $array->map(function ($item){
            return [
                'id' => $item->id,
                'system' => $item->system,
                'value' => $item->value,
                'use' => $item->use
            ];
        });

    }

    private function administrativeGender($payload): ?array
    {
        if(!isset($payload)) return null;

        return [
            'display' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.administrative-genders.show', ['administrative_gender' => $payload->id], false)
                ]
            ]
        ];
    }

    private function address($array){
        if(count($array) === 0) return $array;

        return $array->map(function ($item){
            return [
                'id' => $item->id,
                'use' => $item->use,
                'text' => $item->text,
                'city_code'  => (string) $item->city_code,
                'city_name' => (string) $item->city->name,
                'state_code' => (string) $item->state_code,
                'state_name' => (string) $item->state->name,
            ];
        });

    }

    private function identifier($array){
        if(count($array) === 0) return $array;

        return $array->map(function ($item){
            return [
                'id' => $item->id,
                'identifier_use_id' => $item->identifierUse->id,
                'identifierUse' => $item->identifierUse,
                'identifier_type_id' => $item->identifierType->id,
                'identifierType' => $item->identifierType,
                'value'  => $item->value,
            ];
        });

    }

    private function contact($array){
        if(count($array) === 0) return $array;

        return $array->map(function ($item){
            return [
                'id' => $item->id,
                'given' => $item->given,
                'family' => $item->family,
                'relationship'  => $item->relationship,
                'email' => $item->email,
                'phone' => $item->phone,
            ];
        });

    }

    private function name($array){
        if(count($array) === 0) return $array;

       return $array->map(function ($item){
           return [
               'id' => $item->id,
               'use' => $item->use,
               'given' => $item->given,
               'text' => $item->given." ".$item->father_family." ".$item->mother_family,
               'father_family' => $item->father_family,
               'mother_family' => $item->mother_family,
               '_links' => [
                   'self' => [
                       'href' => route('api.users.show', ['user' => $item->id], false)
                   ]
               ]
           ];
       });

    }


    /**
     * @param $user
     * @return array|null
     */

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
