<?php

namespace App\Http\Requests;

use App\Models\Workarea;

class WorkareaRequest extends FormRequest
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
                    'active' => 'boolean',
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'active' => 'required|boolean'
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Workarea())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
            'active.required' => $this->getRequiredMessage()
        ];
    }

}
