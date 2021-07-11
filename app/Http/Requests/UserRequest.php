<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{

    public function rules(): array
    {

        switch ($this->getMethod()){
            case 'POST':
                return [
                    'rut' => 'required|max:12|string',
                    'names' => 'required|max:200|string',
                    'lastname' => 'required|max:200|string',
                    'mother_lastname' => 'required|max:200|string',
                    'email' => 'required|max:255|email|unique:users',
                    'password' => 'required|string',
                    'phone' => 'string',
                    'created_user_id' => 'integer',
                    'created_user_ip' => 'integer',
                    'active' => 'boolean'
                ];
            case 'PUT':
                return [
                    'rut' => 'max:12|string',
                    'names' => 'max:200|string',
                    'lastname' => 'max:200|string',
                    'mother_lastname' => 'max:200|string',
                    'phone' => 'string',
                    'email' =>[
                        'max:255',
                        'email',
                        Rule::unique('users')->ignore($this->id),
                    ], 'max:255|email|unique:users',
                    'password' => 'string',
                    'active' => 'boolean'
                ];
            default:
                return [];
        }

    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'password' => bcrypt($this->password),
            'rut' => Str::upper($this->rut),
            'names' => Str::upper($this->names),
            'lastname' => Str::upper($this->lastname),
            'mother_lastname' => Str::upper($this->mother_lastname),
        ]);
    }

    public function getPaginate(): int
    {
       return $this->get('paginate', (new User)->getPerPage());
    }

    public function messages(): array
    {
        return [
            'rut.required' => $this->getRequiredMessage(),
            'rut.max' => $this->getMaxMessage(12),
            'rut.string' => $this->getStringMessage(),
            'names.required' =>  $this->getRequiredMessage(),
            'names.max' => $this->getMaxMessage(200),
            'names.string' => $this->getStringMessage(),
            'lastname.required' =>  $this->getRequiredMessage(),
            'lastname.max' => $this->getMaxMessage(200),
            'lastname.string' => $this->getStringMessage(),
            'mother_lastname.required' =>  $this->getRequiredMessage(),
            'mother_lastname.max' => $this->getMaxMessage(200),
            'mother_lastname.string' => $this->getStringMessage(),
            'email.required' =>  $this->getRequiredMessage(),
            'email.max' => $this->getMaxMessage(255),
            'email.unique' => $this->getUniqueMessage(),
            'email.email' => $this->getEmailMessage(),
            'password.required' =>  $this->getRequiredMessage(),
            'password.string' => $this->getStringMessage(),
        ];
    }

    public function attributes(): array
    {
        return  [
            'rut' => 'RUT (rut)',
            'names' => 'Nombres (names)',
            'lastname' => 'Apellido Paterno (lastname)',
            'mother_lastname' => 'Apellido Materno (mother_lastname)',
            'email' => 'Correo electrónico (email)',
            'password' => 'Contraseña (password)'
        ];
    }
}
