<?php


namespace App\Http\Requests;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    public function failedValidation(Validator $validator)
    {
        if($this->isJson())
        {
            $response = response()->json([
                'success' => false,
                'message' => 'Ops! Some errors occurred',
                'data' => NULL,
                'errors' => $validator->errors()
            ]);
        }else{
            $response = response()->json([
                'success' => false,
                'message' => 'Ops! Some errors occurred',
                'data' => NULL,
                'errors' => 'Content-Type debe ser application/json'
            ], 406);
        }

        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getMaxMessage(int $max): string
    {
        return "El atributo :attribute debe tener un máximo de {$max} caracteres.";
    }

    public function getMinMessage(int $min): string
    {
        return "El atributo :attribute debe tener un mínimo de {$min} caracteres.";
    }

    public function getStringMessage(): string
    {
        return "El atributo :attribute debe ser un string.";
    }

    public function getEmailMessage(): string
    {
        return "El atributo :attribute debe ser válido.";
    }

    public function getBooleanMessage(): string
    {
        return "El atributo :attribute debe ser boolean";
    }

    public function getUniqueMessage(): string
    {
        return "El atributo :attribute ya existe.";
    }

    public function getRequiredMessage(): string
    {
        return "El atributo :attribute es obligatorio.";
    }
}
