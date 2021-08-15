<?php

namespace App\Http\Requests;

use App\Models\Practitioner;

class PractitionerRequest extends FormRequest
{
    public function rules(): array
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'rut' => 'string',
                    'given' => 'string',
                    'family' => 'string',
                    'email' => 'email',
                    'phone' => 'string',
                    'administrative_gender_id' => 'integer',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'rut' => 'required|string',
                    'given' => 'required|string',
                    'family' => 'required|string',
                    'email' => 'email',
                    'phone' => 'string',
                    'administrative_gender_id' => 'required|integer',
                    'active' => 'required|boolean'
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Practitioner())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'given.required' => $this->getRequiredMessage(),
            'family.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'alias.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
