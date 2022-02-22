<?php

namespace App\Http\Requests;


use App\Models\NobilisAnalyte;

class NobilisAnalyteRequest extends FormRequest
{

    public function rules()
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'description' => 'string',
                ];
            case 'POST':
                return [
                    'id' => 'required|string',
                    'description' => 'required|string',
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new NobilisAnalyte())->getPerPage());
    }

}
