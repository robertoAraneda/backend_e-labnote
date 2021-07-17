<?php

namespace App\Http\Requests;

use App\Models\SamplingIndication;

class SamplingIndicationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->getMethod()){
            case 'PUT':
                return [
                    'name' => 'string',
                    'active' => 'boolean'
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'active' =>  'required|boolean'
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
        return $this->get('paginate', (new SamplingIndication())->getPerPage());
    }
}