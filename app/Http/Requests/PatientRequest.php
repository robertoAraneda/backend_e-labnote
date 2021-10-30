<?php

namespace App\Http\Requests;

use App\Models\Patient;

class PatientRequest extends FormRequest
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
                    'birthdate' => 'date',
                    'administrative_gender_id' => 'integer',
                    'active' => 'boolean',
                    'identifier.*.id' => 'integer',
                    'identifier.*.identifier_type_id' => 'integer',
                    'identifier.*.value' => 'string',
                    'name.*.id' => 'integer',
                    'name.*.use' => 'string',
                    'name.*.given' => 'string',
                    'name.*.father_family' => 'string',
                    'name.*.mother_family' => 'string',
                    'address.*.id' => 'integer',
                    'address.*.use' => 'string',
                    'address.*.text' => 'string',
                    'address.*.city_code' => 'string',
                    'address.*.state_code' => 'string',
                    'telecom.*.id' => 'sometimes|required|integer',
                    'telecom.*.system' => 'sometimes|required|string',
                    'telecom.*.value' => 'sometimes|required|string',
                    'telecom.*.use' => 'sometimes|required|string',
                    'contact.*.id' => 'integer',
                    'contact.*.given' => 'string',
                    'contact.*.family' => 'string',
                    'contact.*.relationship' => 'string',
                    'contact.*.phone' => 'string',
                    'contact.*.email' => 'email',

                ];
            case 'POST':
                return [
                    'birthdate' => 'required|date',
                    'administrative_gender_id' => 'required|integer',
                    'active' => 'required|boolean',
                    'identifier.*.identifier_type_id' => 'required|integer',
                    'identifier.*.value' => 'required|string',
                    'name.*.use' => 'string',
                    'name.*.given' => 'string',
                    'name.*.father_family' => 'string',
                    'name.*.mother_family' => 'string',
                    'address.*.use' => 'sometimes|required|string',
                    'address.*.text' => 'sometimes|required|string',
                    'address.*.city_code' => 'sometimes|required|string',
                    'address.*.state_code' => 'sometimes|required|string',
                    'telecom.*.system' => 'sometimes|required|string',
                    'telecom.*.value' => 'sometimes|required|string',
                    'telecom.*.use' => 'sometimes|required|string',
                    'contact.*.given' => 'string',
                    'contact.*.family' => 'string',
                    'contact.*.relationship' => 'string',
                    'contact.*.phone' => 'string',
                    'contact.*.email' => 'email',
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
        return $this->get('paginate', (new Patient())->getPerPage());
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'birthdate.required' => $this->getRequiredMessage(),
            'gender_id.required' => $this->getRequiredMessage(),
            'active.required' => $this->getRequiredMessage(),
            'birthdate.date' => $this->getDateMessage(),
            'gender_id.integer' => $this->getIntegerMessage(),
            'active.boolean' => $this->getBooleanMessage(),
        ];
    }
}
