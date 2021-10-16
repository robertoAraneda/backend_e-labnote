<?php

namespace App\Http\Requests;

use App\Models\Slot;

class SlotRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getMethod()){
            case 'PUT':
                return [
                    'slot_status_id' => 'integer',
                    'start' => 'string',
                    'end' => 'string',
                    'slotTime'=> 'integer',
                    'overbooked' => 'boolean',
                    'comment' => 'string',
                ];
            case 'POST':
                return [
                    'dates.end' => 'string',
                    'dates.start' => 'string',
                    'rangeTimeAppointment.start' => 'string',
                    'rangeTimeAppointment.end' => 'string',
                    'slot_status_id' => 'integer',
                    'start' => 'string',
                    'end' => 'string',
                    'slotTime'=> 'required|integer',
                    'overbooked' => 'boolean',
                    'comment' => 'string',
                ];
            default:
                return [];
        }
    }

    /**
     * @return int
     */
    public function getPaginate(): int
    {
        return $this->get('paginate', (new Slot())->getPerPage());
    }

}
