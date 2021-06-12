<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      title="Solicitud HTTP Role Request",
 *      description="Datos para actualizar un rol",
 *      type="object",
 *      required={"name"}
 * )
 */

class RoleRequest extends FormRequest
{

    /**
     * @OA\Property(
     *      title="Nombre",
     *      description="Nombre del Rol",
     *      example="Médico"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="Guard name",
     *      description="Nombre del Guard",
     *      example="api o web"
     * )
     *
     * @var string
     */
    public $guard_name;


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'guard_name' => 'string',
        ];
    }
}
