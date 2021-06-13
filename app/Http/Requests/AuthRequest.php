<?php

namespace App\Http\Requests;


class AuthRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'rut' => 'required|string',
            'password' => 'required',
            'remember_me' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'rut.required' => $this->getRequiredMessage(),
            'rut.string' => $this->getStringMessage(),
            'password.required' => $this->getRequiredMessage(),
            'remember_me.boolean' => $this->getBooleanMessage()
        ];
    }

    public function attributes(): array
    {
        return  [
            'rut' => 'RUT',
            'password' => 'contraseña',
        ];
    }
}
