<?php

namespace App\Http\Requests;

use App\Models\Container;

class ContainerRequest extends FormRequest
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
                    'shortname' => 'string',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'shortname' =>  'required|string',
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
        return $this->get('paginate', (new Container())->getPerPage());
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
            'shortname.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'shortname.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
