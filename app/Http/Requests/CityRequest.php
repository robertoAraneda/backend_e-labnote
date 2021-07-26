<?php

namespace App\Http\Requests;

use App\Models\City;

class CityRequest extends FormRequest
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
                    'code' => 'string',
                    'state_id' => 'integer',
                    'active' => 'boolean',
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'code' =>  'required|string',
                    'state_id' => 'required|integer',
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
        return $this->get('paginate', (new City())->getPerPage());
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
            'code.required' => $this->getRequiredMessage(),
            'state_id.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'code.string' => $this->getStringMessage(),
            'state_id.integer' => $this->getIntegerMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
