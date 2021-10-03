<?php

namespace App\Http\Requests;

use App\Models\SpecimenStatus;

class SpecimenStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        switch ($this->getMethod()){
            case 'PUT':
                return [
                    'code' => 'string',
                    'display' => 'string',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'code' => 'required|string',
                    'display' => 'required|string',
                    'active' =>  'required|boolean'
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
        return $this->get('paginate', (new SpecimenStatus())->getPerPage());
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'display.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'display.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
