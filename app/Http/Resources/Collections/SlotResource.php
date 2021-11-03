<?php

namespace App\Http\Resources\Collections;


use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SlotResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'start' => Carbon::parse($this->start)->format('Y-m-d H:i:s'),
            'end' =>  Carbon::parse($this->end)->format('Y-m-d H:i:s'),
            'slot_status_id' => (int) $this->slot_status_id,
            'comment' => $this->comment,
            'overbooked' => (bool) $this->overbooked,
            '_links' => [
                'self' =>[
                    'href' => route('api.slots.show', ['slot' => $this->id], false),
                ] ,
            ],
        ];
    }
}
