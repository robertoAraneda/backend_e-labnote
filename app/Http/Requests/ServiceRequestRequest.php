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
                    'requisition' => 'string',
                    'note' => 'string',
                    'service_request_status_id' => 'integer',
                    'service_request_intent_id' => 'integer',
                    'service_request_priority_id' => 'integer',
                    'service_request_category_id' => 'integer',
                    'patient_id' => 'integer',
                    'requester_id' => 'integer',
                    'performer_id' => 'integer',
                    'location_id' => 'integer'
                ];
            case 'POST':
                return [
                    'requisition' => 'required|string',
                    'note' => 'required|string',
                    'service_request_status_id' => 'required|integer',
                    'service_request_intent_id' => 'required|integer',
                    'service_request_priority_id' => 'required|integer',
                    'service_request_category_id' => 'required|integer',
                    'patient_id' => 'required|integer',
                    'requester_id' => 'required|integer',
                    'performer_id' => 'required|integer',
                    'location_id' => 'required|integer'
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
