<?php

namespace App\Http\Requests;

use App\Models\Loinc;

class LoincRequest extends FormRequest
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
                    'loinc_num' => 'string',
                    'long_common_name' => 'string'

                ];
            case 'POST':
                return [
                    'loinc_num' => 'required|string',
                    'long_common_name' => 'required|string'

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
        return $this->get('paginate', (new Loinc())->getPerPage());
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'loinc_num.required' => $this->getRequiredMessage(),
            'loinc_common_name.required' => $this->getRequiredMessage(),
            'loinc_num.string' => $this->getStringMessage(),
            'loinc_common_name.string' => $this->getStringMessage(),
        ];
    }
}
