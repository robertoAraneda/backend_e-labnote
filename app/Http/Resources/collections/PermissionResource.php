<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'name' => $this->name,
            'model' => $this->model,
            'guard_name' => $this->guard_name,
            'action' => $this->action,
            'description' => $this->description,
            '_links' => [
                'self' =>[
                    'href' => route('api.permissions.show', ['permission' => $this->id], false),
                ] ,
            ],
        ];
    }
}
