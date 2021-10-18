<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\AppointmentStatus;

class AppointmentRequest extends FormRequest
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
                    'appointment_status_id' => 'integer',
                    'start' => 'string',
                    'end' => 'string',
                    'description' => 'string',
                    'minutes_duration' => 'integer',
                    'patient_id' => 'integer',
                    'service_request_id' => 'integer',
                ];
            case 'POST':
                return [
                    'start' => 'string',
                    'end' => 'string',
                    'description' => 'string',
                    'minutes_duration' => 'integer',
                    'patient_id' => 'required|integer',
                    'slot_id' => 'integer',
                    'service_request_id' => 'integer',
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
        return $this->get('paginate', (new Appointment())->getPerPage());
    }


    protected function prepareForValidation()
    {
        if($this->status){
            $this->merge([
                'appointment_status_id' => AppointmentStatus::where('code', $this->status)->first()->id
            ]);
        }
    }

}
