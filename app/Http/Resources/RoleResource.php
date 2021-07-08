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
            'guardName' => $this->guard_name,
            'active' => (bool) $this->active,
            'createdUser' => $this->created_user->names,
            'createdAt' => $this->created_at->format('d/m/Y')
        ];
    }
}
