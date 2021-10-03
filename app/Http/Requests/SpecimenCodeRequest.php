<?php

namespace App\Http\Requests;

use App\Models\SpecimenCode;

class SpecimenCodeRequest extends FormRequest
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
                    'display' => 'string',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
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
        return $this->get('paginate', (new SpecimenCode())->getPerPage());
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
