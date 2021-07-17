<?php

namespace App\Http\Requests;

use App\Models\Fonasa;

class FonasaRequest extends FormRequest
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
                    'rem_code' => 'string',
                    'mai_code' => 'string',
                    'active' => 'boolean',
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'rem_code' => 'required|string',
                    'mai_code' => 'required|string',
                    'active' => 'required|boolean',
                ];
            default:
                return [];
        }
    }

    /**
     * @return int
     */
    public function getPaginate(): int
    {
        return $this->get('paginate', (new Fonasa())->getPerPage());
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'rem_code.required' => $this->getRequiredMessage(),
            'mai_code.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'rem_code.string' => $this->getStringMessage(),
            'mai_code.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
