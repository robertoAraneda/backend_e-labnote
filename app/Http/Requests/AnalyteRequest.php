<?php

namespace App\Http\Requests;

use App\Models\Analyte;

class AnalyteRequest extends FormRequest
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
                    'is_patient_codable' => 'boolean',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'is_patient_codable' => 'required|boolean',
                    'active' => 'required|boolean'
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
        return $this->get('paginate', (new Analyte())->getPerPage());
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
            'is_patient_codable.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'is_patient_codable.boolean' => $this->getBooleanMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
