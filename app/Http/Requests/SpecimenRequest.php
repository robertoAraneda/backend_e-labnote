<?php

namespace App\Http\Requests;

use App\Models\Specimen;

class SpecimenRequest extends FormRequest
{
    public function rules(): array
    {
        switch ($this->getMethod()){
            case 'PUT':
                return [
                    'accession_identifier' => 'string',
                    'specimen_status_id' => 'integer',
                    'specimen_code_id' => 'integer',
                    'patient_id' =>'integer',
                    'service_request_id' =>'integer',
                    'collected_at' => 'string',
                ];
            case 'POST':
                return [
                    'accession_identifier' => 'string',
                    'specimen_status_id' => 'integer',
                    'specimen_code_id' => 'required|integer',
                    'patient_id' =>'required|integer',
                    'service_request_id' =>'required|integer',
                    'collected_at' => 'string',
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Specimen())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'display.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'display.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
