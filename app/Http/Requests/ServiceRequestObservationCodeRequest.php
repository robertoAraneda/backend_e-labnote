<?php

namespace App\Http\Requests;

use App\Models\ServiceRequestObservationCode;

class ServiceRequestObservationCodeRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'clinical_information' => 'string',
                    'name' => 'string',
                    'container_id' => 'integer',
                    'availability_id' => 'integer',
                    'laboratory_id' => 'integer',
                    'analyte_id' => 'integer',
                    'workarea_id' => 'integer',
                    'process_time_id' => 'integer',
                    'medical_request_type_id' => 'integer',
                    'loinc_num' => 'string',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'clinical_information' => 'required|string',
                    'name' => 'required|string',
                    'container_id' => 'required|integer',
                    'availability_id' => 'required|integer',
                    'laboratory_id' => 'required|integer',
                    'analyte_id' => 'required|integer',
                    'workarea_id' => 'required|integer',
                    'process_time_id' => 'required|integer',
                    'medical_request_type_id' => 'required|integer',
                    'loinc_num' => 'required|string',
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
        return $this->get('paginate', (new ServiceRequestObservationCode())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'clinical_information.required' => $this->getRequiredMessage(),
            'name.required' => $this->getRequiredMessage(),
            'container_id.required' => $this->getRequiredMessage(),
            'specimen_id.required' => $this->getRequiredMessage(),
            'availability_id.required' => $this->getRequiredMessage(),
            'laboratory_id.required' => $this->getRequiredMessage(),
            'analyte_id.required' => $this->getRequiredMessage(),
            'workarea_id.required' => $this->getRequiredMessage(),
            'process_time_id.required' => $this->getRequiredMessage(),
            'medical_request_type_id.required' => $this->getRequiredMessage(),
            'loinc_num.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'clinical_information.string' => $this->getStringMessage(),
            'name.string' => $this->getStringMessage(),
            'container_id.integer' => $this->getIntegerMessage(),
            'specimen_id.integer' => $this->getIntegerMessage(),
            'availability_id.integer' => $this->getIntegerMessage(),
            'laboratory_id.integer' => $this->getIntegerMessage(),
            'analyte_id.integer' => $this->getIntegerMessage(),
            'workarea_id.integer' => $this->getIntegerMessage(),
            'process_time_id.integer' => $this->getIntegerMessage(),
            'medical_request_type_id.integer' => $this->getIntegerMessage(),
            'loinc_num.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }

}
