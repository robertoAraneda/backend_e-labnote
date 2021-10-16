<?php

namespace App\Http\Requests;

use App\Models\AppointmentType;

class AppointmentTypeRequest extends FormRequest
{
    public function rules(): array
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'code' => 'string',
                    'display' => 'string',
                ];
            case 'POST':
                return [
                    'code' => 'required|string',
                    'display' => 'required|string',
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new AppointmentType())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'code.required' => $this->getRequiredMessage(),
            'display.required' => $this->getRequiredMessage(),
            'code.string' => $this->getStringMessage(),
            'display.string' => $this->getStringMessage(),
        ];
    }
}
