<?php

namespace App\Http\Requests;

use App\Models\Laboratory;

class LaboratoryRequest extends FormRequest
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
                    'address' => 'string',
                    'email' => 'email',
                    'phone' => 'string',
                    'redirect' => 'string'
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'address' => 'string',
                    'email' => 'email',
                    'phone' => 'string',
                    'redirect' => 'string'
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Laboratory)->getPerPage());
    }

    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'address.string' => $this->getStringMessage(),
            'email.email' => $this->getEmailMessage(),
            'phone.string' => $this->getStringMessage(),
            'redirect.string' => $this->getStringMessage()

        ];
    }

    public function attributes(): array
    {
        return  [
            'name' => 'Nombre',
            'address' => 'Dirección',
            'email' => 'Correo electrónico',
            'redirect' => 'Dirección http página web',
            'phone' => 'Teléfono'
        ];
    }

}