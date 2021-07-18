<?php

namespace App\Http\Requests;

use App\Models\Role;

class RoleRequest extends FormRequest
{


    /**
     * @return array|string[]
     */
    public function rules(): array
    {

        switch ($this->getMethod()){
            case 'PUT':
                return [
                    'name' => 'string',
                    'active' => 'boolean',
                    'guard_name' => 'string',
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'active' => 'boolean',
                    'guard_name' => 'string',
                ];
            default:
                return [];
        }
    }


    public function getPaginate(): int
    {
        return $this->get('paginate', (new Role())->getPerPage());
    }


    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'guard_name.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage()
        ];
    }
}
