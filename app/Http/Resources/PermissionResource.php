<?php

namespace App\Http\Resources;

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
        if(isset($this->checkbox)){
            return [
                'id' => $this->id,
                'name' => $this->name,
                'model' => $this->model,
                'action' => $this->action,
                'description' => $this->description,
                'checkbox' =>(bool) $this->checkbox
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model' => $this->model,
            'action' => $this->action,
            'description' => $this->description,
        ];
    }
}
