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
                    'slug' => 'string',
                    'clinical_information' => 'string',
                    'loinc_id' => 'string',
                    'process_time_id' => 'integer',
                    'workarea_id' => 'integer',
                    'availability_id' => 'integer',
                    'medical_request_type_id' => 'integer',
                    'is_patient_codable' => 'boolean',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'slug' => 'required|string',
                    'clinical_information' => 'required|string',
                    'loinc_id' => 'required|string',
                    'process_time_id' => 'required|integer',
                    'workarea_id' => 'required|integer',
                    'availability_id' => 'required|integer',
                    'medical_request_type_id' => 'required|integer',
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
            'slug.required' => $this->getRequiredMessage(),
            'clinical_information.required' => $this->getRequiredMessage(),
            'loinc_id.required' => $this->getRequiredMessage(),
            'workarea_id.required' => $this->getRequiredMessage(),
            'availability_id.required' => $this->getRequiredMessage(),
            'process_time_id.required' => $this->getRequiredMessage(),
            'medical_request_type_id.required' => $this->getRequiredMessage(),
            'is_patient_codable.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'slug.string' => $this->getStringMessage(),
            'clinical_information.string' => $this->getStringMessage(),
            'loinc_id.string' => $this->getStringMessage(),
            'workarea_id.integer' => $this->getIntegerMessage(),
            'availability_id.integer' => $this->getIntegerMessage(),
            'process_time_id.integer' => $this->getIntegerMessage(),
            'medical_request_type_id.integer' => $this->getIntegerMessage(),
            'is_patient_codable.boolean' => $this->getBooleanMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
