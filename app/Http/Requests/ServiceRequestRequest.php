<?php

namespace App\Http\Requests;

use App\Models\ServiceRequest;

class ServiceRequestRequest extends FormRequest
{
    public function rules(): array
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'note' => 'string',
                    'diagnosis' => 'string',
                    'requisition' => 'string',
                    'service_request_priority_id' => 'integer',
                    'patient_id' => 'integer',
                    'performer_id' => 'integer',
                    'location_id' => 'integer',
                    'confidential_specimens.*.specimen_code_id' => 'integer',
                    'confidential_specimens.*.patient_id' => 'integer',
                    'confidential_specimens.*.container_id' => 'integer',
                    'not_confidential_specimens.*.specimen_code_id' => 'integer',
                    'not_confidential_specimens.*.patient_id' => 'integer',
                    'not_confidential_specimens.*.container_id' => 'integer',
                    'specimens.*.specimen_code_id' => 'required|integer',
                    'specimens.*.patient_id' => 'required|integer',
                    'specimens.*.container_id' => 'required|integer',
                    'observations.*.service_request_observation_code_id' => 'required|integer',
                    'confidential_observations.*.service_request_observation_code_id' => 'integer',
                    'not_confidential_observations.*.service_request_observation_code_id' => 'integer',
                ];
            case 'POST':
                return [
                    'note' => 'string',
                    'occurrence' => 'required|string',
                    'diagnosis' => 'string',
                    'service_request_priority_id' => 'required|integer',
                    'patient_id' => 'required|integer',
                    'performer_id' => 'required|integer',
                    'location_id' => 'required|integer',
                    'confidential_specimens.*.specimen_code_id' => 'integer',
                    'confidential_specimens.*.patient_id' => 'integer',
                    'confidential_specimens.*.container_id' => 'integer',
                    'not_confidential_specimens.*.specimen_code_id' => 'integer',
                    'not_confidential_specimens.*.patient_id' => 'integer',
                    'not_confidential_specimens.*.container_id' => 'integer',
                    'specimens.*.specimen_code_id' => 'required|integer',
                    'specimens.*.patient_id' => 'required|integer',
                    'specimens.*.container_id' => 'required|integer',
                    'observations.*.service_request_observation_code_id' => 'required|integer',
                    'confidential_observations.*.service_request_observation_code_id' => 'integer',
                    'not_confidential_observations.*.service_request_observation_code_id' => 'integer',
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new ServiceRequest())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'alias.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'alias.string' => $this->getStringMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
