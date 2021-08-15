<?php

namespace App\Http\Requests;

use App\Models\Location;

class LocationRequest extends FormRequest
{
    public function rules(): array
    {
        switch ($this->getMethod()) {
            case 'PUT':
                return [
                    'name' => 'string',
                    'alias' => 'string',
                    'location_status_id'=> 'integer',
                    'description'=> 'string',
                    'location_type_id'=> 'integer',
                    'location_physical_type_id'=> 'integer',
                    'managing_organization_id'=> 'integer',
                    'part_of_location_id'=> 'integer',
                ];
            case 'POST':
                return [
                    'name' => 'required|string',
                    'alias' => 'required|string',
                    'location_status_id'=> 'required|integer',
                    'description'=> 'required|string',
                    'location_type_id'=> 'required|integer',
                    'location_physical_type_id'=> 'required|integer',
                    'managing_organization_id'=> 'integer',
                    'part_of_location_id'=> 'integer',
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', (new Location())->getPerPage());
    }

    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'alias.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'alias.string' => $this->getStringMessage(),
        ];
    }
}
