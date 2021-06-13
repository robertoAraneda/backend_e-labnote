<?php

namespace App\Http\Requests;

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
    protected string $name;

    /**
     * @OA\Property(
     *      title="Guard name",
     *      description="Nombre del Guard",
     *      example="api o web"
     * )
     *
     * @var string
     */
    protected string $guard_name;

    public function rules(): array
    {

        switch ($this->getMethod()){
            case 'PUT':
            case 'POST':
                return [
                    'name' => 'required|string',
                    'guard_name' => 'string',
                ];
            default:
                return [];
        }
    }

    public function getPaginate(): int
    {
        return $this->get('paginate', 10);
    }

    public function messages(): array
    {
        return [
            'name.required' => $this->getRequiredMessage(),
            'name.string' => $this->getStringMessage(),
            'guard_name.string' => $this->getStringMessage()
        ];
    }

    public function attributes(): array
    {
        return  [
            'name' => 'Nombre',
            'guard_name' => 'Puerta',
        ];
    }
}
