<?php

namespace App\Http\Requests;

use App\Models\Specimen;

class SpecimenRequest extends FormRequest
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
                    'name' => 'string',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
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
        return $this->get('paginate', (new Specimen())->getPerPage());
    }


    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}