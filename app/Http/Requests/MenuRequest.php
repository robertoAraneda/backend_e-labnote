<?php

namespace App\Http\Requests;

use App\Models\Menu;

class MenuRequest extends FormRequest
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
                    'module_id' => 'integer',
                    'status' => 'integer',
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'module_id' => 'required|integer',
                    'status' => 'integer'
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Menu)->getPerPage());
    }

    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'module_id.required' => $this->getRequiredMessage(),
            'module_id.integer' => $this->getIntegerMessage(),
            'status.integer' => $this->getIntegerMessage()
        ];
    }

    public function attributes(): array
    {
        return  [
            'name' => 'name',
            'module_id' => 'module_id',
            'status' => 'status'
        ];
    }
}
