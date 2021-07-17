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
                    'name' => 'string',
                    'icon' => 'string',
                    'url' => 'string',
                    'slug' => 'string',
                    'active' => 'boolean',
                    ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'url' => 'string',
                    'icon' => 'string',
                    'slug' => 'string',
                    'active' => 'boolean'
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
            'name.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'name.icon' => $this->getStringMessage(),
            'name.url' => $this->getStringMessage(),
            'name.slug' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage()
        ];
    }

}
