<?php

namespace App\Http\Requests;

use App\Models\Disponibility;

class DisponibilityRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Disponibility())->getPerPage());
    }
}
