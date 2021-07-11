<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'rut' => $this->rut,
            'names' => $this->names,
            'lastname' => $this->lastname,
            'mother_lastname' => $this->mother_lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_user_ip' => $this->created_user_ip,
            'updated_user_ip' => $this->updated_user_ip,
            'deleted_user_ip' => $this->deleted_user_ip,
            'created_at' => $this->date($this->created_at),
            'updated_at' => $this->date($this->updated_at),
            'deleted_at' => $this->date($this->deleted_at),
            'active' => (bool) $this->active,
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $this->id], false),
                ]
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
                'deletedUser' => $this->user($this->deletedUser)
            ],
        ];
    }

    private function date($date){
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
