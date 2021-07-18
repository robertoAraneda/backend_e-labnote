<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'guard_name' => $this->guard_name,
            'active' => (bool) $this->active,
            'created_at' => $this->created_at->format('d/m/Y'),
            '_links' => [
                'self' => [
                    'href' => route('api.roles.show', ['role' => $this->id], false),
                ],
            ],
        ];
    }
}
