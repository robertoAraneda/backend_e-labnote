<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
                'description' => $this->description,
                'status' => intval($this->status),
                'checkbox' =>(bool) $this->checkbox
            ];
        }
        return [
            'id' => $this->id,
            'description' => $this->description,
            'status' => intval($this->status),
        ];

    }
}
