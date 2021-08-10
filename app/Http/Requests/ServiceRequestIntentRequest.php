<?php

namespace App\Http\Requests;

use App\Models\ServiceRequestIntent;

class ServiceRequestIntentRequest extends FormRequest
{

    public function rules(): array
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'code' => 'string',
                    'display' => 'string',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'code' => 'required|string',
                    'display' => 'required|string',
                    'active' => 'required|boolean'
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new ServiceRequestIntent())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'code.required' => $this->getRequiredMessage(),
            'display.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'code.string' => $this->getStringMessage(),
            'display.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
