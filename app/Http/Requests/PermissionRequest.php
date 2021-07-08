<?php

namespace App\Http\Requests;


use App\Models\Permission;

class PermissionRequest extends FormRequest
{

    protected string $name;

    protected string $guard_name;

    public function rules(): array
    {
        switch ($this->getMethod()){
            case 'PUT':
            case 'POST':
                return [
                    'name' => 'required|string',
                    'model' => 'string',
                    'action' => 'string',
                    'description' => 'string',
                    'guard_name' => 'string',
                ];
            default:
                return [];
        }

    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Permission)->getPerPage());
    }

    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'guard_name.string' => $this->getStringMessage()
        ];
    }

    public function attributes(): array
    {
        return  [
            'name' => 'Nombre',
            'guard_name' => 'Tipo de puerta',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['id' => $this->route('id')]);
    }
}
