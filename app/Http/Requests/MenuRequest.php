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
                    'icon' => 'string',
                    'url' => 'string',
                    'module_id' => 'integer',
                    'active' => 'boolean',
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'icon' => 'string',
                    'url' => 'string',
                    'module_id' => 'required|integer',
                    'active' => 'boolean'
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
            'url.string' => $this->getStringMessage(),
            'icon.string' => $this->getStringMessage(),
            'module_id.required' => $this->getRequiredMessage(),
            'module_id.integer' => $this->getIntegerMessage(),
            'active.boolean' => $this->getBooleanMessage()
        ];
    }

    public function attributes(): array
    {
        return  [
            'name' => 'name',
            'icon' => 'icon',
            'url' => 'url',
            'module_id' => 'module_id',
            'active' => 'active'
        ];
    }
}
