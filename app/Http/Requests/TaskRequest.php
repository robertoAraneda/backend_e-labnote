<?php

namespace App\Http\Requests;

use App\Models\Task;

class TaskRequest extends FormRequest
{
    public function rules(): array
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'based_on' => 'string',
                    'business_status_id' => 'string',
                ];
            case 'POST':
                return [
                    'based_on' => 'required|integer',
                    'business_status_id' => 'required|string',
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Task())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'code.required' => $this->getRequiredMessage(),
            'display.required' => $this->getRequiredMessage(),
            'code.string' => $this->getStringMessage(),
            'display.string' => $this->getStringMessage()
        ];
    }

}
