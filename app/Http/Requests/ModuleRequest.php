<?php

namespace App\Http\Requests;

use App\Models\Module;

class ModuleRequest extends FormRequest
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
                    'description' => 'string',
                    'status' => 'number',
                    ];
            case 'POST':
                return [
                    'description' => 'required|string',
                    'status' => 'integer'
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Module)->getPerPage());
    }

    public function messages(): array
    {
        return [
            'description.required' => $this->getRequiredMessage(),
            'description.string' => $this->getStringMessage(),
            'status.integer' => $this->getIntegerMessage()
        ];
    }

    public function attributes(): array
    {
        return  [
            'description' => 'Nombre'
        ];
    }
}
