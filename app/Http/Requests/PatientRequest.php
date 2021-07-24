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
                    'patient.birthdate' => 'date',
                    'patient.administrative_gender_id' => 'integer',
                    'humanName.id' => 'integer',
                    'humanName.use' => 'string',
                    'humanName.given' => 'string',
                    'humanName.father_family' => 'string',
                    'humanName.mother_family' => 'string',
                    'addressPatient.id' => 'sometimes|required|integer',
                    'addressPatient.use' => 'sometimes|required|string',
                    'addressPatient.text' => 'sometimes|required|string',
                    'addressPatient.city_id' => 'sometimes|required|integer',
                    'addressPatient.district_id' => 'sometimes|required|integer',
                    'addressPatient.state_id' => 'sometimes|required|integer',
                    'contactPointPatient.*.id' => 'sometimes|required|integer',
                    'contactPointPatient.*.system' => 'sometimes|required|string',
                    'contactPointPatient.*.value' => 'sometimes|required|string',
                    'contactPointPatient.*.use' => 'sometimes|required|string',
                    'contactPatient.*.id' => 'sometimes|required|integer',
                    'contactPatient.*.given' => 'sometimes|required|string',
                    'contactPatient.*.family' => 'sometimes|required|string',
                    'contactPatient.*.relationship' => 'sometimes|required|string',
                    'contactPatient.*.phone' => 'sometimes|required|string',
                    'contactPatient.*.email' => 'sometimes|required|email',

                ];
            case 'POST':
                return [
                    'patient.birthdate' => 'required|date',
                    'patient.administrative_gender_id' => 'required|integer',
                    'identifier.*.identifier_uses_id' => 'required|integer',
                    'identifier.*.identifier_type_id' => 'required|integer',
                    'identifier.*.value' => 'required|integer',
                    'humanName.use' => 'string',
                    'humanName.given' => 'string',
                    'humanName.father_family' => 'string',
                    'humanName.mother_family' => 'string',
                    'addressPatient.*.use' => 'sometimes|required|string',
                    'addressPatient.*.text' => 'sometimes|required|string',
                    'addressPatient.*.city_id' => 'sometimes|required|integer',
                    'addressPatient.*.district_id' => 'sometimes|required|integer',
                    'addressPatient.*.state_id' => 'sometimes|required|integer',
                    'contactPointPatient.*.system' => 'sometimes|required|string',
                    'contactPointPatient.*.value' => 'sometimes|required|string',
                    'contactPointPatient.*.use' => 'sometimes|required|string',
                    'contactPatient.*.given' => 'sometimes|required|string',
                    'contactPatient.*.family' => 'sometimes|required|string',
                    'contactPatient.*.relationship' => 'sometimes|required|string',
                    'contactPatient.*.phone' => 'sometimes|required|string',
                    'contactPatient.*.email' => 'sometimes|required|email',
                    'patient.active' => 'required|boolean',
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
