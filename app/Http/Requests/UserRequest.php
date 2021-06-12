<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
    protected string $name;

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

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
       return $this->get('paginate', 10);
    }
}
