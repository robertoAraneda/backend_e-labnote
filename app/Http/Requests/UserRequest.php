<?php

namespace App\Http\Requests;

use App\Models\User;

/**
 * @OA\Schema(
 *      title="Solicitud HTTP User Request",
 *      description="Datos para actualizar un usuario",
 *      type="object",
 *      required={"name, rut"}
 * )
 */
class UserRequest extends FormRequest
{

    /**
     * @OA\Property(
     *      title="Nombre",
     *      description="Nombre del Usuario",
     *      example="Roberto Alejandro"
     * )
     *
     * @var string
     */
    protected string $names;

    /**
     * @OA\Property(
     *      title="Nombre",
     *      description="Rut del usuario",
     *      example="12.345.678-9"
     * )
     *
     * @var string
     */
    protected string $rut;

    protected string $lastname;

    protected string $mother_lastname;

    protected string $password;

    /**
     * @OA\Property(
     *      title="Email",
     *      description="Correo electrónico del usuario",
     *      example="robaraneda@gmail.com"
     * )
     *
     * @var string
     */
    protected string $email;

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
                    'password' => 'required|string'
                ];
            case 'PUT':
                return [
                    'rut' => 'max:12|string',
                    'names' => 'max:200|string',
                    'lastname' => 'max:200|string',
                    'mother_lastname' => 'max:200|string',
                    'email' => 'max:255|email|unique:users',
                    'password' => 'string'
                ];
            default:
                return [];
        }

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
            'rut' => 'RUT',
            'names' => 'Nombres',
            'lastname' => 'Apellido Paterno',
            'mother_lastname' => 'Apellido Materno',
            'email' => 'Correo electrónico',
            'password' => 'contraseña'
        ];
    }
}
